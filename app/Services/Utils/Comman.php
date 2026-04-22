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
        $this->app->cli = config('app.use_cli', false);
        $this->app->mode = config('app.api_mode', 'development');
        $this->app->host_portal_dev = config('app.host_portal_dev');
        $this->app->host_portal_pro = config('app.host_portal_pro');
        $this->app->portal = config('app.portal');
        $this->app->portal_key = config('app.portal_key');
        $this->app->encryption = config('app.encriptation');
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
