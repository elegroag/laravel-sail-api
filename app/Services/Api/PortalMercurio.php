<?php

namespace App\Services\Api;

use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;

class PortalMercurio extends ApiAbstract
{

    private $host_portal_dev;
    private $host_portal_pro;
    private $portal;

    public function __construct()
    {
        $this->mode = config('app.api_mode', 'development');
        $this->host_portal_dev = config('app.host_portal_dev');
        $this->host_portal_pro = config('app.host_portal_pro');
        $this->portal = config('app.portal');
    }

    public function send($attr)
    {
        $servicio = $attr['servicio'];
        $params = isset($attr['params']) ? $attr['params'] : null;
        $user = isset($attr['user']) ? $attr['user'] : 2;

        if (is_array($params)) {
            $params = array_merge($params, [
                '_user' => $user,
                '_sistema' => 'Mercurio',
                '_env' => $this->mode,
            ]);
        } else {
            $params = [
                '_user' => $user,
                '_sistema' => 'Mercurio',
                '_env' => $this->mode,
            ];
        }

        $basicAuth = new BasicAuth(config('app.host_api_user'), config('app.host_api_password'));
        if ($this->mode == 'development') {
            $hostConnection = "{$this->host_portal_dev}/";
        } else {
            $hostConnection = "{$this->host_portal_pro}/";
        }
        $url = $this->portal . "/{$servicio}.php";

        $this->setCurlCommand($hostConnection, $url, $params, $basicAuth);

        $api = new APIClient($basicAuth, $hostConnection, $url);
        $api->setTypeJson(false);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        return $this;
    }

    public function setCurlCommand($hostConnection, $url, $params, $basicAuth)
    {
        $token = $basicAuth->authenticate();
        $this->lineaComando = "curl -X POST {$hostConnection}/{$url} \"" .
            " -H 'Content-Type: application/json' " .
            " -H 'Authorization: Basic {$token}'" .
            " -d \"" . json_encode($params) . "\" \"";
    }
}
