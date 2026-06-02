<?php

namespace App\Services\Api;

use App\Exceptions\DebugException;
use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\ApiEndpoint;
use Illuminate\Support\Facades\Log;

class ApiSubsidio extends ApiAbstract
{

    public function __construct()
    {
        $this->mode = config('app.api_mode', 'development');
    }

    public function send(array $attr)
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

        $basicAuth = new BasicAuth(config('app.host_api_user'), config('app.host_api_password'));

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException('Error no es valido el metodo de acceso API ', 501);
        }
        $endpoint = ApiEndpoint::where('connection_name', 'api-clisisu')
            ->where('service_name', $servicio)
            ->first();

        #Log::info('[ApiSubsidio] Servicio: ' . $servicio . ' | Metodo: ' . $metodo . ' | Endpoint: ' . ($endpoint ? $endpoint->endpoint_name : 'NULL'));

        $hostConnection = $this->mode == 'development' ? $endpoint->host_dev : $endpoint->host_pro;
        #Log::info('[ApiSubsidio] Host: ' . $hostConnection . ' | Mode: ' . $this->mode);
        // $basicAuth->encript($this->app->encryption, $this->app->portal_clave);

        $url = "{$endpoint->endpoint_name}/{$metodo}";
        $this->setCurlCommand($hostConnection, $url, $params, $basicAuth);

        $api = new APIClient($basicAuth, $hostConnection, $url);
        $api->setTypeJson(true);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        log::info('[ApiSubsidio] Respuesta API:', is_string($this->output) ? [$this->output] : (array) $this->output);

        return $this;
    }

    public function setCurlCommand(string $hostConnection, string $url, array $params, BasicAuth $basicAuth)
    {
        $token = $basicAuth->authenticate();
        $this->lineaComando = "curl -X POST {$hostConnection}/{$url} \"" .
            " -H 'Content-Type: application/json' " .
            " -H 'Authorization: Basic {$token}'" .
            " -d \"" . json_encode($params) . "\" \"";
    }
}
