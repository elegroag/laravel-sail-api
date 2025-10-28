<?php

namespace App\Jobs;

use App\Models\Comandos;
use App\Models\ComandoEstructuras;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class RunExternalCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public int $comandoId;

    /**
     * Crear una nueva instancia del Job.
     */
    public function __construct(int $comandoId)
    {
        $this->onQueue('commands');
        $this->comandoId = $comandoId;
    }

    /**
     * Ejecuta el proceso externo y persiste el estado/salida.
     */
    public function handle(): void
    {
        $cmd = Comandos::query()->findOrFail($this->comandoId);
        $plantilla = ComandoEstructuras::query()
            ->where('estructura', $cmd->estructura)
            ->firstOrFail();

        $this->ensureLogsDir();
        $logPath = storage_path('logs/commands/'.($cmd->id).'.log');

        // Marcar en ejecución
        $cmd->estado = 'E';
        $cmd->resultado = $logPath;
        $cmd->save();

        $env = [];
        if (!empty($plantilla->env)) {
            $envDecoded = json_decode((string)$plantilla->env, true);
            if (is_array($envDecoded)) {
                $env = $envDecoded;
            }
        }

        $process = Process::fromShellCommandline($cmd->linea_comando, base_path(), $env, null, null);
        $process->setTimeout(null);

        $this->appendLog($logPath, "[INICIO] ".date('c')."\n");
        $process->run(function ($type, $buffer) use ($logPath) {
            $this->appendLog($logPath, $buffer);
        });

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

        $cmd->save();
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
}
