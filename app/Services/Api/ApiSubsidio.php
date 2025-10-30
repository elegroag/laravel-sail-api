<?php

namespace App\Services\Api;

use App\Exceptions\DebugException;
use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\ApiEndpoint;
use App\Models\Gener02;

class ApiSubsidio extends ApiAbstract
{
    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
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
                '_env' => $this->app->mode,
                '_base64' => ($base64 == 1),
            ]);
        } else {
            if (is_null($params) == false) {
                $metodo .= '/' . $params;
            }
            $params = [
                '_user' => 2,
                '_sistema' => 'Mercurio', // Core::getInstanceName(),
                '_env' => $this->app->mode,
                '_base64' => ($base64 == 1),
            ];
        }

        $userApi = Gener02::where('usuario', '2')->first();
        $basicAuth = new BasicAuth('2', $userApi->clave);

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException('Error no es valido el metodo de acceso API ', 501);
        }
        $endpoint = ApiEndpoint::where('connection_name', 'api-clisisu')
            ->where('service_name', $servicio)
            ->first();

        $hostConnection = env('API_MODE') == 'development' ? $endpoint->host_dev : $endpoint->host_pro;
        // $basicAuth->encript($this->app->encryption, $this->app->portal_clave);

        $url = "{$endpoint->endpoint_name}/{$metodo}";
        $this->lineaComando = $hostConnection . "\n" . $url . "\n" . json_encode($params);

        $api = new APIClient($basicAuth, $hostConnection, $url);
        $api->setTypeJson(true);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        return $this;
    }
}
