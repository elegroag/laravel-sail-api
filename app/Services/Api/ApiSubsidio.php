<?php

namespace App\Services\Api;

use App\Exceptions\DebugException;
use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\ApiEndpoint;

class ApiSubsidio extends ApiAbstract
{

    public function __construct()
    {
        $this->mode = env('API_MODE', 'development');
    }

    public function send($attr)
    {
        $servicio = $attr['servicio'];
        $metodo = isset($attr['metodo']) ? $attr['metodo'] : null;
        $params = isset($attr['params']) ? $attr['params'] : null;
        $base64 = isset($attr['base64']) ? $attr['base64'] : 0;

        if (is_array($params)) {
            $params = array_merge($params, [
                '_user' => 2,
                '_sistema' => 'Mercurio', // Core::getInstanceName(),
                '_env' => $this->mode,
                '_base64' => ($base64 == 1),
            ]);
        } else {
            if (is_null($params) == false) {
                $metodo .= '/' . $params;
            }
            $params = [
                '_user' => 2,
                '_sistema' => 'Mercurio', // Core::getInstanceName(),
                '_env' => $this->mode,
                '_base64' => ($base64 == 1),
            ];
        }

        $basicAuth = new BasicAuth(env('HOST_API_USER'), env('HOST_API_PASSWORD'));

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException('Error no es valido el metodo de acceso API ', 501);
        }
        $endpoint = ApiEndpoint::where('connection_name', 'api-clisisu')
            ->where('service_name', $servicio)
            ->first();

        $hostConnection = $this->mode == 'development' ? $endpoint->host_dev : $endpoint->host_pro;
        // $basicAuth->encript($this->app->encryption, $this->app->portal_clave);

        $url = "{$endpoint->endpoint_name}/{$metodo}";
        $this->setCurlCommand($hostConnection, $url, $params, $basicAuth);

        $api = new APIClient($basicAuth, $hostConnection, $url);
        $api->setTypeJson(true);
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
