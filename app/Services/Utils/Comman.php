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
        $this->app = new \stdClass;
        $this->app->cli = env('USE_CLI', false);
        $this->app->mode = env('API_MODE', 'development');
        $this->app->host_portal_dev = env('HOST_PORTAL_DEV', 'http://localhost:8000');
        $this->app->host_portal_pro = env('HOST_PORTAL_PRO', 'http://localhost:8000');
        $this->app->portal = env('PORTAL', 'Portal');
        $this->app->portal_key = env('PORTAL_KEY', 'PortalKey');
        $this->app->encryption = env('API_ENCRYPTION', 'ApiEncryption');
    }

    public function __construct()
    {
        $this->initEnvironment();
    }

    /**
     * runCli function
     * Adapter consumo de servicio cliente
     *
     * @return ApiSubsidio
     */
    public function send($attr, $base64 = null)
    {
        $attr['base64'] = $base64;
        $this->procesadorComandos = new ApiSubsidio();
        $this->procesadorComandos->send($attr);
    }

    /**
     * runPortal function
     *
     * @return PortalMercurio
     */
    public function runPortal($attr)
    {
        $this->procesadorComandos = new PortalMercurio();
        $this->procesadorComandos->send($attr);
    }

    /**
     * init function
     *
     * @return ProcesadorComandos
     */
    public static function init($procesador = 'p7')
    {
        $comman = new Comman;
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

    public function toArray()
    {
        return $this->procesadorComandos->toArray();
    }

    public function getObject()
    {
        return $this->procesadorComandos->getObject();
    }
}
