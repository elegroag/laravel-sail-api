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
        parent::__construct();
    }

    public function send($attr)
    {
        $servicio = isset($attr['servicio']) ? $attr['servicio'] : null;
        $metodo = isset($attr['metodo']) ? $attr['metodo'] : null;
        $params = isset($attr['params']) ? $attr['params'] : null;

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException("Error no es valido el metodo de acceso API ", 501);
        }
        $basicAuth = new BasicAuth(env('API_WHATSAPP_USER', '2'), env('API_WHATSAPP_PASSWORD', 'ok'));

        $api_end_point = ApiEndpoint::where('connection_name', 'api-whatsapp')
            ->where('service_name', $servicio)
            ->first();

        $hostConnection = env('API_MODE') == 'development' ? $api_end_point->host_dev : $api_end_point->host_pro;
        $url = "{$api_end_point->endpoint_name}/{$metodo}";
        $this->lineaComando =  $hostConnection . "\n " . $url . "\n " . json_encode($params);

        $api = new APIClient($basicAuth, $hostConnection,  $url);
        $api->setTypeJson(true);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );
        return $this;
    }
}
