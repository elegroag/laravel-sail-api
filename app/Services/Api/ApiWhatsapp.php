<?php

namespace App\Services\Api;

use App\Exceptions\DebugException;
use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\ApiEndpoint;

class ApiWhatsapp extends ApiAbstract
{
    public function __construct()
    {
        $this->mode = config('app.api_mode', 'development');
    }

    public function send($attr)
    {
        $servicio = isset($attr['servicio']) ? $attr['servicio'] : null;
        $metodo = isset($attr['metodo']) ? $attr['metodo'] : null;
        $params = isset($attr['params']) ? $attr['params'] : null;

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException('Error no es valido el metodo de acceso API ', 501);
        }
        $basicAuth = new BasicAuth(config('app.api_whatsapp_user', '2'), config('app.api_whatsapp_password'));

        $api_end_point = ApiEndpoint::where('connection_name', 'api-whatsapp')
            ->where('service_name', $servicio)
            ->first();

        $hostConnection = config('app.api_mode') == 'development' ? $api_end_point->host_dev : $api_end_point->host_pro;
        $url = "{$api_end_point->endpoint_name}/{$metodo}";
        $this->lineaComando = $hostConnection . "\n " . $url . "\n " . json_encode($params);

        $api = new APIClient($basicAuth, $hostConnection, $url);
        $api->setTypeJson(true);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        return $this;
    }
}
