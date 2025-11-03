<?php

namespace App\Library\ProcesadorComandos;

use App\Exceptions\DebugException;
use App\Models\ComandoEstructuras;
use App\Models\Comandos;

class ProcesadorComandos
{
    private $procesador;

    private $linea_comando;

    private $userAuth;

    private $outPutText;

    private $proceso;

    private $procesarAsyncrono;

    private $exitCode;

    private $stderr;

    private $timeoutSec = 60;

    private $allowedExecutables = [
        'php',
        '/usr/bin/php',
        '/usr/bin/php8.4',
        'bash',
        '/bin/bash',
        'python3',
        '/usr/bin/python3',
        'uv'
    ];

    public function __construct($procesador = 'p7')
    {
        $this->userAuth = session('user') ?? null;
        if (! isset($this->userAuth['usuario'])) {
            $this->userAuth['usuario'] = ((isset($this->userAuth['usuario'])) ? $this->userAuth['usuario'] : 1);
        }
        $this->procesador = $procesador;
        $this->procesarAsyncrono = '';
        $this->exitCode = null;
        $this->stderr = '';
    }

    /**
     * listarComandosRunner function
     * listar los comandos lanzados en un proceso determinado
     *
     * @return void
     */
    public function listarComandosRunner()
    {
        $pattern = escapeshellarg($this->procesador);
        // Excluir el propio grep y devolver líneas completas de ps
        return shell_exec("ps aux | grep -i -- $pattern | grep -v grep");
    }

    /**
     * procesoRunning function
     * buscar el proceso que esta corriendo
     *
     * @return void
     */
    public function procesoRunning()
    {
        $pattern = trim("{$this->procesador} {$this->linea_comando}");
        $pattern = escapeshellarg($pattern);
        $this->proceso = 0;
        // Obtener PID por awk de la salida de ps, columna 2
        $cmd = "ps aux | grep -i -- $pattern | grep -v grep | awk '{print $2}' | head -n1";
        $pid = trim(shell_exec($cmd) ?? '');
        if ($pid !== '' && ctype_digit($pid)) {
            $this->proceso = (int) $pid;
        }
    }

    /**
     * runnerComando function
     * correr los comandos
     *
     * @return void
     */
    public function runnerComando()
    {
        $proc = trim($this->procesador);
        $cmd = trim($this->linea_comando);
        if ($proc === '' || $cmd === '') {
            $this->outPutText = '';
            return;
        }
        if (! $this->isExecutableAllowed($proc)) {
            $this->outPutText = '';
            $this->stderr = 'Ejecutable no permitido por la política de seguridad';
            $this->exitCode = 126; // permiso denegado
            return;
        }
        // Capturar stderr también
        if ($this->procesarAsyncrono == '&') {
            $pid = shell_exec("$cmd > /dev/null 2>&1 & echo $!");
            $pid = trim($pid ?? '');
            $this->proceso = ctype_digit($pid) ? (int) $pid : 0;
            $this->exitCode = null;
            $this->stderr = '';
        } else {
            $this->outPutText = shell_exec("$cmd 2>&1");
            $this->exitCode = $this->procesoRunning();
        }
    }

    /**
     * checkServiceRunner function
     * buscar los servicios
     *
     * @param [type] $servicio
     * @return void
     */
    public function checkServiceRunner($servicio)
    {
        $lcr = "\n" . $this->listarComandosRunner();
        $lista_comandos_runner = explode("\n", $lcr);
        $service_runner = [];
        foreach ($lista_comandos_runner as $linea) {
            if (trim($linea) == '') {
                continue;
            }
            if (@preg_match("/(" . preg_quote($servicio, '/') . ")/i", $linea) === 1) {
                // tomar PID (columna 2 de ps aux)
                $cols = preg_split('/\s+/', trim($linea));
                if (isset($cols[1]) && ctype_digit($cols[1])) {
                    $service_runner[] = (int) $cols[1];
                }
            }
        }

        return $service_runner;
    }

    /**
     * dispararComando function
     * Pasa comando de forma opcional, id del comando opcional
     *
     * @param  int  $id
     * @return void
     */
    public function dispararComando(Comandos $comando, $id = 0)
    {
        if ($id) {
            $comando = Comandos::where('id', $id)->first();
        }
        $this->linea_comando = $comando->linea_comando;
        $startedAt = microtime(true);
        // Persistir started_at en parametros
        $this->mergeTimingIntoParametros($comando, [
            'started_at' => $this->formatIsoTime($startedAt),
        ]);
        $this->runnerComando();
        $finishedAt = microtime(true);
        if ($comando) {
            $comando = Comandos::where('id', $comando->id)->first();
        } else {
            $comando = Comandos::where('id', $id)->first();
        }
        if (empty($this->procesarAsyncrono)) {
            $comando->progreso = 100;
            $comando->estado = 'F';
        } else {
            $comando->progreso = 1;
        }
        $comando->proceso = $this->proceso;
        // Persistir finished_at y duration_ms en parametros
        $this->mergeTimingIntoParametros($comando, [
            'finished_at' => $this->formatIsoTime($finishedAt),
            'duration_ms' => (int) round(($finishedAt - $startedAt) * 1000),
            'exit_code' => $this->exitCode,
        ]);
        $comando->save();
    }

    /**
     * argumentosServicio function
     * se prepara los argumentos para procesar la linea de comandos
     *
     * @param [type] $estructura
     * @param [type] $variables
     * @param [type] $params
     * @param  int  $base64
     * @return string
     */
    public function argumentosServicio($estructura, $variables, $params, $base64 = 0)
    {
        $patrones = [];
        if (is_array($variables)) {
            foreach ($variables as $var) {
                $key = trim((string) $var);
                if ($key === '') {
                    continue;
                }

                $valor = '';
                if (array_key_exists($key, (array) $params)) {
                    $raw = $params[$key];
                    // Para 'context' u otros valores complejos, aplicar JSON y opcionalmente base64
                    if ($key === 'context') {
                        if (is_array($raw) || is_object($raw)) {
                            $encoded = json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            $encoded = (string) $raw;
                        }
                        $valor = $base64 ? base64_encode((string) $encoded) : $encoded;
                    } else {
                        if (is_array($raw) || is_object($raw)) {
                            $encoded = json_encode($raw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            $valor = $encoded;
                        } else {
                            $valor = $raw;
                        }
                    }
                }
                $patrones['/({{' . preg_quote($key, '/') . '}})/'] = $valor;
            }
        }

        $cmd = preg_replace(array_keys($patrones), array_values($patrones), (string) $estructura);
        if (strpos($cmd, '`') !== false) {
            throw new DebugException('Estructura de comando inválida', 501);
        }
        return $this->procesador . ' ' . $cmd;
    }

    /**
     * runnerCliPhp function
     * Correr el comando clisisu para procesar un servicio o comando
     *
     * @param [type] $id
     * @param [type] $params
     * @param [type] $nohub = TRUE ejecuta en segundo plano
     * @return void
     */
    public function runnerCliPhp($nombre = '', array $params = [], $nohub = false)
    {
        if (! $nombre) {
            throw new DebugException('Error el nombre de la infraestructura no es valido', 501);
        }
        $comandoEstructura = ComandoEstructuras::where('nombre', $nombre)->first();
        if (! $comandoEstructura) {
            throw new DebugException('Estructura de comando no encontrada', 501);
        }

        $estructura = $comandoEstructura->estructura;
        $variables = explode('|', $comandoEstructura->variables);

        if ($estructura === null || $estructura === '') {
            throw new DebugException('La estructura del comando está vacía', 501);
        }
        $this->procesarAsyncrono = ($comandoEstructura->asyncro == 1) ? '&' : '';
        $parametros = (isset($params['params'])) ? json_encode($params['params']) : '{}';

        $estado = ($nohub) ? 'P' : 'E';
        // agrega el comando a los parametros
        $comando = $this->crearComando($comandoEstructura->id, $parametros, $estado);
        $params['comando'] = $comando->id;

        $this->linea_comando = $this->argumentosServicio($estructura, $variables, $params, true);
        $comando->linea_comando = $this->linea_comando;
        // Marcar inicio en parametros
        $this->mergeTimingIntoParametros($comando, [
            'started_at' => $this->formatIsoTime(microtime(true)),
        ]);
        $comando->save();

        // los comandos asyncronos no se ejecutan se ejecutan por nohup
        if ($nohub == false) {
            $this->dispararComando($comando);
        }
    }

    /**
     * crearComando function
     *
     * @param [type] $idEstructura
     * @param [type] $parametros
     * @return Comandos
     */
    public function crearComando($idEstructura, $parametros, $estado = 'E')
    {
        $comando = new Comandos;
        $comando->fecha_runner = date('Y-m-d');
        $comando->hora_runner = date('H:i:s');
        $comando->estado = $estado;
        $comando->usuario = $this->userAuth['usuario'];
        $comando->progreso = 1;
        $comando->proceso = 0;
        $comando->linea_comando = '0';
        $comando->estructura = $idEstructura;
        $comando->parametros = $parametros;
        $comando->save();
        return $comando;
    }

    /**
     * runCli function
     * procesar comando sin seguimiento
     *
     * @param [type] $id
     * @param  array  $params
     * @return void
     */
    public function runCli($nombre, $params, $base64 = true)
    {
        if (! $nombre) {
            throw new DebugException('Error el nombre de la infraestructura no es valido', 501);
        }
        $comandoEstructura = ComandoEstructuras::where('nombre', $nombre)->first();
        if (! $comandoEstructura) {
            throw new DebugException('Estructura de comando no encontrada', 501);
        }
        $estructura = $comandoEstructura->estructura;
        $variables = explode('|', $comandoEstructura->variables);

        if ($estructura === null || $estructura === '') {
            throw new DebugException('La estructura del comando está vacía', 501);
        }
        $this->procesarAsyncrono = ($comandoEstructura->asyncro == 1) ? '&' : '';
        $this->linea_comando = trim($this->argumentosServicio($estructura, $variables, $params, $base64));
        $this->runnerComando();

        return $this->getOutPutText();
    }

    public function detenerComando(Comandos $comando)
    {
        $proceso = (int) ($comando->proceso ?? 0);
        if ($proceso === 0) {
            // Fallback: intentar descubrir PID si no está guardado
            $this->linea_comando = $comando->linea_comando;
            $this->procesoRunning();
        } else {
            $this->proceso = $proceso;
        }
        $comando->progreso = 100;
        $comando->estado = 'X';
        $comando->save();

        if ($this->proceso > 0) {
            // Verificación con posix_kill si está disponible
            if (function_exists('posix_kill')) {
                // Primero SIGTERM y luego SIGKILL si persiste
                @posix_kill($this->proceso, 15);
                usleep(200000);
                if ($this->isProcessAlive($this->proceso)) {
                    @posix_kill($this->proceso, 9);
                }
            } else {
                // Fallback a kill -9 si no existe posix_kill
                @shell_exec("kill -9 {$this->proceso}");
            }
        }
        // Marcar finalización por cancelación
        $this->mergeTimingIntoParametros($comando, [
            'finished_at' => $this->formatIsoTime(microtime(true)),
        ]);
        $comando->save();

        return "Proceso {$this->proceso} detenido";
    }

    public function getProceso()
    {
        return $this->proceso;
    }

    public function setLineaComando($linea_comando)
    {
        $this->linea_comando = $linea_comando;
    }

    public function getLineaComando()
    {
        return $this->linea_comando;
    }

    public function getOutPutText()
    {
        return $this->outPutText;
    }

    public function getStderr()
    {
        return $this->stderr;
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function setTimeoutSec($seconds)
    {
        $this->timeoutSec = max(0, (int) $seconds);
    }

    public function setAllowedExecutables(array $executables)
    {
        $this->allowedExecutables = array_values($executables);
    }

    private function isExecutableAllowed($proc)
    {
        $proc = trim((string) $proc);
        if ($proc === '') {
            return false;
        }
        // comparar por ruta completa o basename
        $base = basename($proc);
        foreach ($this->allowedExecutables as $allowed) {
            if ($proc === $allowed || $base === basename($allowed)) {
                return true;
            }
        }
        return false;
    }

    /**
     * toArray function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->outPutText, true);
    }

    /**
     * getObject function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return object
     */
    public function getObject()
    {
        return json_decode($this->outPutText);
    }

    public function comandoEjecutando($servicio, $proceso = '')
    {
        if (! is_string($servicio) || $servicio === '') {
            return null;
        }
        $usuario = $this->userAuth['usuario'] ?? '';
        // Escapar comodines para LIKE
        $pattern = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $servicio) . '%';

        return Comandos::where('usuario', $usuario)
            ->where('estado', 'E')
            ->where('linea_comando', 'like', $pattern)
            ->first();
    }

    public function isJson()
    {
        if (! is_string($this->outPutText) || trim($this->outPutText) === '') {
            return false;
        }
        json_decode($this->outPutText);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    private function isProcessAlive($pid)
    {
        $pid = (int) $pid;
        if ($pid <= 0) {
            return false;
        }
        if (function_exists('posix_kill')) {
            return @posix_kill($pid, 0);
        }
        // Fallback en sistemas sin posix: verificar /proc
        return @file_exists("/proc/{$pid}");
    }

    private function mergeTimingIntoParametros(Comandos $comando, array $timing)
    {
        $current = [];
        if (! empty($comando->parametros)) {
            $decoded = json_decode($comando->parametros, true);
            if (is_array($decoded)) {
                $current = $decoded;
            }
        }
        $current['_timing'] = array_merge($current['_timing'] ?? [], $timing);
        $comando->parametros = json_encode($current, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function formatIsoTime($microTs)
    {
        $sec = (int) $microTs;
        $ms = (int) round(($microTs - $sec) * 1000);
        return gmdate('Y-m-d\TH:i:s', $sec) . sprintf('.%03dZ', $ms);
    }
}
