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
        $this->mode = env('API_MODE', 'development');
        $this->host_portal_dev = env('HOST_PORTAL_DEV');
        $this->host_portal_pro = env('HOST_PORTAL_PRO');
        $this->portal = env('PORTAL');
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

        $basicAuth = new BasicAuth(env('HOST_API_USER'), env('HOST_API_PASSWORD'));
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
        $this->lineaComando = "curl -X POST {$hostConnection}/{$url} \"" .
            " -H 'Content-Type: application/json' " .
            " -H 'Authorization: Basic " . $basicAuth->getHeader() . "'" .
            " -d \"" . json_encode($params) . "\" \"";
    }
}
