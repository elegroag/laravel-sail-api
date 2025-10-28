<?php

namespace App\Services;

use App\Jobs\RunExternalCommand;
use App\Models\Comandos;
use App\Models\ComandoEstructuras;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class NohupRunner
{
    // Servicio de orquestación para ejecutar comandos definidos por ComandoEstructuras

    /**
     * Dispara la ejecución según la plantilla (sync/async) y retorna el registro Comandos.
     */
    public function dispatch(string $name_estructura, array $params = [], ?int $userId = null): Comandos
    {
        // Ahora se busca por 'nombre' para evitar ambigüedades
        $plantilla = ComandoEstructuras::query()
            ->where('nombre', $name_estructura)
            ->firstOrFail();

        // Render de los argumentos/variables
        [$commandLine, $env] = $this->buildCommandLine($plantilla, $params);

        // Crear registro en comandos como Pendiente
        $cmd = new Comandos();
        $cmd->estructura = $plantilla->estructura;
        $cmd->usuario = $userId;
        $cmd->linea_comando = $commandLine;
        $cmd->parametros = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $cmd->estado = 'P';
        $cmd->progreso = 0;
        $cmd->fecha_runner = date('Y-m-d');
        $cmd->hora_runner = date('H:i:s');
        $cmd->save();

        // Decidir sync o async
        $isAsync = (int)($plantilla->asyncro ?? 0) === 1;
        if ($isAsync) {
            // Encolar job
            RunExternalCommand::dispatch($cmd->id)->onQueue('commands');
        } else {
            // Ejecutar en foreground
            $this->runSync($cmd, $env);
        }

        return $cmd;
    }

    /**
     * Ejecuta el comando de forma síncrona en este proceso (bloqueante).
     */
    public function runSync(Comandos $cmd, array $env = []): void
    {
        $this->ensureLogsDir();
        $logPath = storage_path('logs/commands/'.($cmd->id).'.log');

        $cmd->estado = 'E';
        $cmd->resultado = $logPath;
        $cmd->save();

        $process = $this->makeProcessFrom($cmd->linea_comando, $env);
        $process->setTimeout(null);

        // Redirigir salida a archivo
        $this->appendLog($logPath, "[INICIO] ".date('c')."\n");
        $process->run(function ($type, $buffer) use ($logPath) {
            $this->appendLog($logPath, $buffer);
        });

        // Guardar PID si disponible
        if (method_exists($process, 'getPid')) {
            $pid = $process->getPid();
            if ($pid) {
                $cmd->proceso = (string)$pid;
            }
        }

        if ($process->isSuccessful()) {
            $cmd->estado = 'F';
            $cmd->progreso = 100;
            $this->appendLog($logPath, "\n[FIN OK] ".date('c')."\n");
        } else {
            $cmd->estado = 'X';
            $this->appendLog($logPath, "\n[FIN ERROR] Exit=".$process->getExitCode()." ".date('c')."\n");
        }

        // Guardar últimas líneas como resumen
        $cmd->resultado = $logPath;
        $cmd->save();
    }

    /**
     * Encola el Job (interfaz pública por si se necesita invocar desde otro flujo).
     */
    public function runAsync(Comandos $cmd): void
    {
        RunExternalCommand::dispatch($cmd->id)->onQueue('commands');
    }

    /**
     * Cancela un proceso si hay PID conocido.
     */
    public function cancel(Comandos $cmd): bool
    {
        $pid = $cmd->proceso ? (int)$cmd->proceso : 0;
        if ($pid <= 0) {
            return false;
        }

        $ok = false;
        if (function_exists('posix_kill')) {
            $ok = @posix_kill($pid, SIGTERM);
            if (!$ok) {
                $ok = @posix_kill($pid, SIGKILL);
            }
        } else {
            @exec('kill -TERM '.escapeshellarg((string)$pid).' >/dev/null 2>&1', $o, $code);
            $ok = ($code === 0);
        }

        if ($ok) {
            $cmd->estado = 'X';
            $cmd->save();
        }
        return $ok;
    }

    /**
     * Consulta estado y últimas líneas del log.
     */
    public function status(int $comandoId, int $tail = 100): array
    {
        $cmd = Comandos::query()->findOrFail($comandoId);
        $logPath = is_string($cmd->resultado ?? null) ? $cmd->resultado : null;
        $lastLines = $logPath && is_file($logPath) ? $this->tailFile($logPath, $tail) : [];
        return [
            'id' => $cmd->id,
            'estado' => $cmd->estado,
            'progreso' => $cmd->progreso,
            'pid' => $cmd->proceso,
            'log' => $logPath,
            'tail' => $lastLines,
        ];
    }

    /**
     * Construye la línea de comando y entorno a partir de la plantilla.
     */
    private function buildCommandLine(ComandoEstructuras $plantilla, array $params): array
    {
        // Soporta dos formatos:
        // 1) Formato JSON (script/args) en variables
        // 2) Formato estilo seeder: plantilla con placeholders {{var}} y variables "a|b|c"

        $env = [];
        if (!empty($plantilla->env)) {
            $envDecoded = json_decode((string)$plantilla->env, true);
            if (is_array($envDecoded)) {
                $env = $envDecoded;
            }
        }

        $variablesRaw = (string)($plantilla->variables ?? '');
        $variablesDecoded = json_decode($variablesRaw, true);

        // Caso 1: variables como JSON
        if (is_array($variablesDecoded)) {
            $variables = $variablesDecoded;
            $vars = array_merge($variables, $params);
            $binary = $this->resolveBinary((string)$plantilla->procesador);

            $args = [];
            if (isset($vars['script'])) {
                $args[] = (string)$vars['script'];
            }
            if (isset($vars['args']) && is_array($vars['args'])) {
                foreach ($vars['args'] as $a) {
                    $args[] = (string)$a;
                }
            }

            $escaped = array_map(fn($v) => (string)$v, $args);
            $commandLine = trim(implode(' ', array_filter([$binary, ...$escaped])));
            return [$commandLine, $env];
        }

        // Caso 2: formato seeder (plantilla con placeholders)
        $template = (string)($plantilla->estructura ?? '');
        // variables viene como "a|b|c"
        $names = array_filter(array_map('trim', explode('|', $variablesRaw)));
        foreach ($names as $name) {
            $value = $params[$name] ?? '';
            $template = str_replace('{{'.$name.'}}', $this->escapeArg($value), $template);
        }

        // Si la plantilla no tiene binario explícito, intentar prefijar por procesador
        $trimmed = ltrim($template);
        if ($trimmed === '' || preg_match('/^(\/|\.|[A-Za-z0-9_-]+\s)/', $trimmed)) {
            // Parece ser una línea completa de shell (ruta absoluta/relativa o ya con comando). Usar tal cual.
            $commandLine = trim(preg_replace('/\s+/', ' ', $template));
        } else {
            // Fallback: prefijar con binario resuelto
            $binary = $this->resolveBinary((string)$plantilla->procesador);
            $commandLine = trim($binary.' '.trim(preg_replace('/\s+/', ' ', $template)));
        }

        return [$commandLine, $env];
    }

    /**
     * Crea un Process a partir de una línea de comando.
     */
    private function makeProcessFrom(string $commandLine, array $env = []): Process
    {
        // Ejecutar en shell para mayor compatibilidad de rutas/alias
        $process = Process::fromShellCommandline($commandLine, base_path(), $env, null, null);
        return $process;
    }

    private function resolveBinary(string $procesador): string
    {
        return match ($procesador) {
            'php', 'p7' => 'php',
            'py' => 'python3',
            'javac' => 'javac', // si se requiere ejecución, usar wrapper que luego corra `java`
            'npm' => 'node', // o `npm run <script>` si corresponde; manejar vía variables
            default => $procesador ?: 'sh',
        };
    }

    private function ensureLogsDir(): void
    {
        $dir = storage_path('logs/commands');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }

    private function appendLog(string $path, string $content): void
    {
        @file_put_contents($path, $content, FILE_APPEND);
    }

    private function escapeArg($value): string
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if ($value === null) {
            return "''";
        }
        // Comillas seguras para shell
        return escapeshellarg((string)$value);
    }

    private function tailFile(string $filePath, int $lines = 100): array
    {
        if (!is_file($filePath)) return [];
        $content = @file($filePath);
        if (!$content) return [];
        return array_slice($content, max(0, count($content) - $lines));
    }
}
