<?php
namespace App\Services\Utils;

use App\Library\ProcesadorComandos\ProcesadorComandos;
use App\Models\ComandoEstructuras;
use App\Models\Comandos;
use App\Services\Api\ApiSubsidio;
use App\Services\Api\PortalMercurio;

class Comman 
{
    protected $procesadorComandos = null;
    protected $app;
    public $models;


    public function __construct()
    {
        $con = (object) CoreConfig::readFromActiveApplication('config.ini');
        $this->app = $con->apisisu;
        $this->models = new stdClass();
        $this->models->Comandos = new Comandos;
        $this->models->ComandoEstructuras = new ComandoEstructuras;
    }

    /**
     * runCli function
     * Adapter consumo de servicio cliente
     * @return ApiSubsidio
     */
    public function runCli($attr, $base64 = null)
    {
        $attr['base64'] = $base64;
        $this->procesadorComandos = new ApiSubsidio($this->app);
        $this->procesadorComandos->send($attr);
    }

    public function runPortal($attr)
    {
        $attr['base64'] = 0;
        $this->procesadorComandos = new PortalMercurio($this->app);
        $this->procesadorComandos->send($attr);
    }

    /**
     * init function
     * @param string $procesador
     * @return object
     */
    public static function init($procesador = 'p7')
    {
        $comman = new Comman();
        if ($comman->app->cli) {
            $comman->procesadorComandos = new ProcesadorComandos($comman->models, $procesador);
            return $comman->procesadorComandos;
        } else {
            return $comman;
        }
    }

    /**
     * Api function
     * @changed [2024-04-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return Comman
     */
    public static function Api()
    {
        $comman = new Comman();
        return $comman;
    }

    /**
     * Cli function
     * @return void
     */
    public static function Cli($procesador = 'p7')
    {
        $comman = new Comman();
        $comman->procesadorComandos = new ProcesadorComandos($comman->models, $procesador);
        return $comman->procesadorComandos;
    }

    public function getLineaComando()
    {
        return $this->procesadorComandos->getlineaComando();
    }

    public function isJson()
    {
        return $this->procesadorComandos->isJson();
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
        return $this->procesadorComandos->toArray();
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
        return $this->procesadorComandos->getObject();
    }
}
