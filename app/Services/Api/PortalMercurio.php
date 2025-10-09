<?php

namespace App\Services\Api;

use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\Gener02;

class PortalMercurio extends ApiAbstract
{
    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
    }

    public function send($attr)
    {
        $servicio = $attr['servicio'];
        $params = isset($attr['params']) ? $attr['params'] : null;
        $user = isset($attr['user']) ? $attr['user'] : 2;

        if (is_array($params)) {
            $params = array_merge($params, [
                '_user' => $user,
                '_sistema' => 'Mercurio', // Core::getInstanceName(),
                '_env' => $this->app->mode,
            ]);
        } else {
            $params = [
                '_user' => $user,
                '_sistema' => 'Mercurio', // Core::getInstanceName(),
                '_env' => $this->app->mode,
            ];
        }

        $userApi = (new Gener02)->findFirst("usuario='{$user}'");
        $basicAuth = new BasicAuth('2', $userApi->getClave());

        if ($this->app->mode == 'development') {
            $hostConnection = "{$this->app->host_portal_dev}/";
        } else {
            // $basicAuth->encript($this->app->encryption, $this->app->portal_clave);
            $hostConnection = "{$this->app->host_portal_pro}/";
        }
        $url = $this->app->portal."/{$servicio}.php";

        $this->lineaComando = $hostConnection."\n".$url."\n".json_encode($params);
        $api = new APIClient($basicAuth, $hostConnection, $url);
        $api->setTypeJson(false);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        return $this;
    }
}
