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

    public function __construct($procesador = 'p7')
    {
        $this->userAuth = session('user') ?? null;
        if (! isset($this->userAuth['usuario'])) {
            $this->userAuth['usuario'] = ((isset($this->userAuth['usuario'])) ? $this->userAuth['usuario'] : 1);
        }
        $this->procesador = $procesador;
        $this->procesarAsyncrono = '';
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
        $line = trim($this->linea_comando);
        if ($proc === '' || $line === '') {
            $this->outPutText = '';
            return;
        }
        // Capturar stderr también
        if ($this->procesarAsyncrono == '&') {
            $pid = shell_exec("$proc $line > /dev/null 2>&1 & echo $!");
            $pid = trim($pid ?? '');
            $this->proceso = ctype_digit($pid) ? (int) $pid : 0;
        } else {
            $this->outPutText = shell_exec("$proc $line 2>&1");
            $this->procesoRunning();
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
        $this->runnerComando();
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
        $comando->save();
    }

    /**
     * argumentosServicio function
     * se prepara los argumentos para procesar la linea de comandos
     *
     * @param [type] $estructura
     * @param [type] $params
     * @param  int  $base64
     * @return void
     */
    public function argumentosServicio($estructura, $params, $base64 = 0)
    {
        $patrones = [
            '/({{servicio}})/' => isset($params['servicio']) ? $params['servicio'] : '',
            '/({{metodo}})/' => isset($params['metodo']) ? $params['metodo'] : '',
            '/({{params}})/' => isset($params['params']) ? (($base64) ? base64_encode(json_encode($params['params'])) : $params['params']) : '',
            '/({{user}})/' => $this->userAuth['usuario'],
            '/({{sistema}})/' => env('APP_NAME'),
            '/({{env}})/' => isset($params['env']) ? $params['env'] : '1',
            '/({{comando}})/' => isset($params['comando']) ? $params['comando'] : '',
        ];

        return preg_replace(array_keys($patrones), array_values($patrones), $estructura);
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
    public function runnerCliPhp($idEstructura = 0, array $params = [], $nohub = false)
    {
        if (! $idEstructura) {
            throw new DebugException('Error el id de la infraestructura no es valido', 501);
        }
        $comandoEstructura = ComandoEstructuras::where('id', $idEstructura)->first();
        if (! $comandoEstructura) {
            throw new DebugException('Estructura de comando no encontrada', 501);
        }
        $estructura = $comandoEstructura->estructura;
        if ($estructura === null || $estructura === '') {
            throw new DebugException('La estructura del comando está vacía', 501);
        }
        $this->procesarAsyncrono = ($comandoEstructura->asyncro == 1) ? '&' : '';
        $parametros = (isset($params['params'])) ? json_encode($params['params']) : '{}';

        $estado = ($nohub) ? 'P' : 'E';
        // agrega el comando a los parametros
        $comando = (object) $this->crearComando($comandoEstructura->id, $parametros, $estado);
        $params['comando'] = $comando->id;

        $this->linea_comando = (string) $this->argumentosServicio($estructura, $params, true);
        $comando->linea_comando = $this->linea_comando;
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
     * @return void
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
    public function runCli($id, $params, $base64 = true)
    {
        if (! $id) {
            throw new DebugException('Error el id de la infraestructura no es valido', 501);
        }
        $comandoEstructura = ComandoEstructuras::where('id', $id)->first();
        if (! $comandoEstructura) {
            throw new DebugException('Estructura de comando no encontrada', 501);
        }
        $estructura = $comandoEstructura->estructura;
        if ($estructura === null || $estructura === '') {
            throw new DebugException('La estructura del comando está vacía', 501);
        }
        $this->procesarAsyncrono = ($comandoEstructura->asyncro == 1) ? '&' : '';
        $this->linea_comando = trim((string) $this->argumentosServicio($estructura, $params, $base64));
        $this->runnerComando();

        return $this->getOutPutText();
    }

    public function detenerComando(Comandos $comando)
    {
        $proceso = $comando->proceso;
        if ($proceso == 0 || empty($proceso) || is_null($proceso)) {
            $this->linea_comando = $comando->linea_comando;
            $this->procesoRunning();
        } else {
            $this->proceso = $proceso;
        }
        $comando->progreso = 100;
        $comando->estado = 'X';
        $comando->save();

        return shell_exec("kill -9 {$this->proceso}");
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
}
