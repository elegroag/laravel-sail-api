<?php
namespace App\Services\Utils;

use App\Library\ProcesadorComandos\ProcesadorComandos;
use App\Services\Api\ApiSubsidio;
use App\Services\Api\PortalMercurio;

class Comman 
{
    protected $procesadorComandos = null;
    protected $app;
    public $models;

    public function initEnvironment()
    {
        $this->app = [
            "cli" => env('USE_CLI', false),
            "mode" => env('API_MODE', 'development'),
            "host_portal_dev" => env('HOST_PORTAL_DEV', 'http://localhost:8000'),
            "host_portal_pro" => env('HOST_PORTAL_PRO', 'http://localhost:8000'),
            "portal" => env('PORTAL', 'Portal'),
            "portal_key" => env('PORTAL_KEY', 'PortalKey'),
            "encryption" => env('API_ENCRYPTION', 'ApiEncryption'),
        ];
    }

    public function __construct()
    {
        $this->initEnvironment();
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
            $comman->procesadorComandos = new ProcesadorComandos($procesador);
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
        $comman = self::init($procesador);
        $comman->procesadorComandos = new ProcesadorComandos($procesador);
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
