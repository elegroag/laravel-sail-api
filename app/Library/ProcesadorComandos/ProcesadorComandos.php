<?php
namespace App\Library\ProcesadorComandos;

use App\Exceptions\DebugException;
use App\Models\Comandos;

class ProcesadorComandos
{

    private $procesador;
    private $linea_comando;
    private $Comandos;
    private $ComandoEstructuras;
    private $userAuth;
    private $outPutFile;
    private $outPutText;
    private $proceso;
    private $procesarAsyncrono;

    public function __construct($component, $procesador = 'p7')
    {
        if (session()->has('documento')) {
            $this->userAuth = session()->all();
        }
        if (!isset($this->userAuth['usuario'])) $this->userAuth['usuario'] = ((isset($this->userAuth['documento'])) ? $this->userAuth['documento'] : 1);
        $this->procesador = $procesador;
        $this->Comandos = $component->Comandos;
        $this->ComandoEstructuras = $component->ComandoEstructuras;
        $this->outPutFile = storage_path('logs/' . $this->userAuth['usuario'] . '_' . strtotime('now') . '.log');
        $this->procesarAsyncrono = '';
    }

    /**
     * listarComandosRunner function
     * listar los comandos lanzados en un proceso determinado
     * @return void
     */
    public function listarComandosRunner()
    {
        return shell_exec("ps aux | grep '{$this->procesador}'");
    }

    /**
     * procesoRunning function
     * buscar el proceso que esta corriendo
     * @return void
     */
    public function procesoRunning()
    {
        $lineas = array();
        exec("ps aux | grep '{$this->procesador} {$this->linea_comando}'", $lineas);
        $this->proceso = 0;

        //retorna siempre más de 2 lineas de salida, cuando el comando esta en ejecucion
        if (count($lineas) >= 3) {
            $linea_proceso = trim(substr($lineas[0], 6, 10));
            $exp = explode(" ", $linea_proceso);
            if (!empty($exp[0])) {
                $this->proceso = $exp[0];
            }
        }
    }

    /**
     * runnerComando function
     * correr los comandos
     * @return void
     */
    function runnerComando()
    {
        if ($this->procesarAsyncrono == '&') {
            $this->proceso = shell_exec("{$this->procesador} {$this->linea_comando} > /dev/null 2>&1 & echo $!");
        } else {
            $this->outPutText = shell_exec("{$this->procesador} {$this->linea_comando}");
            $this->procesoRunning();
        }
    }

    /**
     * checkServiceRunner function
     * buscar los servicios
     * @param [type] $servicio
     * @return void
     */
    public function checkServiceRunner($servicio)
    {
        $lcr = "\n" . $this->listarComandosRunner();
        $lista_comandos_runner = explode("\n", $lcr);
        $service_runner = array();
        foreach ($lista_comandos_runner as $linea) {
            if (trim($linea) == '') continue;
            if (preg_match("/({$servicio})/i", $linea) !== false) {
                if (strpos($linea, 'grep') !== false) continue;
                $linea_proceso = trim(substr($linea, 6, 10));
                $exp = explode(" ", $linea_proceso);
                if (!empty($exp[0])) {
                    $service_runner[] = $exp[0];
                }
            }
        }
        return $service_runner;
    }

    /**
     * dispararComando function
     * Pasa comando de forma opcional, id del comando opcional
     * @param Comandos $comando
     * @param integer $id
     * @return void
     */
    public function dispararComando(Comandos $comando, $id = 0)
    {
        if ($id) {
            $comando = $this->Comandos->findFirst(" id='{$id}'");
        }
        $this->linea_comando = $comando->getLineaComando();
        $this->runnerComando();
        if ($comando) {
            $comando = $this->Comandos->findFirst(" id='{$comando->getId()}'");
        } else {
            $comando = $this->Comandos->findFirst(" id='{$id}'");
        }
        if (empty($this->procesarAsyncrono)) {
            $comando->setProgreso(100);
            $comando->setEstado('F');
        } else {
            $comando->setProgreso(1);
        }
        $comando->setProceso($this->proceso);
        $comando->save();
    }

    /**
     * argumentosServicio function
     * se prepara los argumentos para procesar la linea de comandos
     * @param [type] $estructura
     * @param [type] $params
     * @param integer $base64
     * @return void
     */
    function argumentosServicio($estructura, $params, $base64 = 0)
    {
        $patrones = array(
            "/({{servicio}})/" => isset($params['servicio']) ? $params['servicio'] : '',
            "/({{metodo}})/" => isset($params['metodo']) ? $params['metodo'] : '',
            "/({{params}})/" => isset($params['params']) ? (($base64) ? base64_encode(json_encode($params['params'])) : $params['params']) : '',
            "/({{user}})/" => $this->userAuth['usuario'],
            "/({{sistema}})/" => env('APP_NAME'),
            "/({{env}})/" => isset($params['env']) ? $params['env'] : '1',
            "/({{comando}})/" => isset($params['comando']) ? $params['comando'] : ''
        );
        return preg_replace(array_keys($patrones), array_values($patrones), $estructura);
    }

    /**
     * runnerCliPhp function
     * Correr el comando clisisu para procesar un servicio o comando 
     * @param [type] $id 
     * @param [type] $params 
     * @param [type] $nohub = TRUE ejecuta en segundo plano
     * @return void
     */
    public function runnerCliPhp($idEstructura = 0, array $params = array(), $nohub = false)
    {
        if (!$idEstructura) {
            throw new DebugException("Error el id de la infraestructura no es valido", 501);
        }
        $comandoEstructura = $this->ComandoEstructuras->findFirst("id='{$idEstructura}'");
        $estructura = $comandoEstructura->getEstructura();
        $this->procesarAsyncrono = ($comandoEstructura->getAsyncro() == 1) ? '&' : '';
        $parametros = (isset($params['params'])) ? json_encode($params['params']) : '{}';

        $estado = ($nohub) ? 'P' : 'E';
        //agrega el comando a los parametros
        $comando = (object) $this->crearComando($comandoEstructura->getId(), $parametros, $estado);
        $params['comando'] = $comando->getId();
        $this->linea_comando = $this->argumentosServicio($estructura, $params, true);
        $comando->setLineaComando($this->linea_comando);
        $comando->save();

        //los comandos asyncronos no se ejecutan se ejecutan por nohup
        if ($nohub == false) {
            $this->dispararComando($comando);
        }
    }

    /**
     * crearComando function
     * @param [type] $idEstructura
     * @param [type] $parametros
     * @return void
     */
    public function crearComando($idEstructura, $parametros, $estado = 'E')
    {
        $idComnado = $this->Comandos->maximum('id') + 1;
        $comando = new Comandos();
        $comando->setId($idComnado);
        $comando->setFechaRunner(date('Y-m-d'));
        $comando->setHoraRunner(date('H:i:s'));
        $comando->setEstado($estado);
        $comando->setUsuario($this->userAuth['usuario']);
        $comando->setProgreso(1);
        $comando->setProceso(0);
        $comando->setLineaComando('0');
        $comando->setEstructura($idEstructura);
        $comando->setParametros($parametros);
        if (!$comando->save()) {
            throw new DebugException("Error al guardar el comando", 501);
        }
        return $comando;
    }

    /**
     * runCli function
     * procesar comando sin seguimiento
     * @param [type] $id
     * @param array $params
     * @return void
     */
    public function runCli($id, $params, $base64 = true)
    {
        if (!$id) {
            throw new DebugException("Error el id de la infraestructura no es valido", 501);
        }
        $comandoEstructura = $this->ComandoEstructuras->findFirst("id='{$id}'");
        $estructura = $comandoEstructura->getEstructura();
        $this->procesarAsyncrono = ($comandoEstructura->getAsyncro() == 1) ? '&' : '';
        $this->linea_comando = $this->argumentosServicio($estructura, $params, $base64);
        $this->runnerComando();
        return $this->getOutPutText();
    }

    public function detenerComando(Comandos $comando)
    {
        $proceso = $comando->getProceso();
        if ($proceso == 0 || empty($proceso) || is_null($proceso)) {
            $this->linea_comando = $comando->getLineaComando();
            $this->procesoRunning();
        } else {
            $this->proceso = $proceso;
        }
        $comando->setProgreso(100);
        $comando->setEstado('X');
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
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->outPutText, true);
    }

    /**
     * getObject function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return object
     */
    public function getObject()
    {
        return json_decode($this->outPutText);
    }

    public function comandoEjecutando($servicio, $proceso = '')
    {
        return $this->Comandos->findFirst(" usuario='{$this->userAuth['usuario']}' and estado='E' and (linea_comando like '%{$servicio}%')");
    }

    public function isJson()
    {
        $macth = preg_match('/^\{(.*)\:(.*)\}$/', trim($this->outPutText));
        if ($macth === 0 || $macth === false) {
            $macth = preg_match('/^\[(.*)\:(.*)\]$/', trim($this->outPutText));
        }
        if ($macth === 0 || $macth === false) {
            return false;
        } else {
            return true;
        }
    }
}
