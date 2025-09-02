<?php
namespace App\Services\Api;

use App\Exceptions\DebugException;
use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\Adapter\DbBase;
use function App\Library\APIClient\mapper_sisu_api;

class ApiSubsidio extends ApiAbstract
{

    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
        $this->db = DbBase::rawConnect();
    }

    public function send($attr)
    {
        $servicio = $attr['servicio'];
        $metodo = isset($attr['metodo']) ? $attr['metodo'] : null;
        $params = isset($attr['params']) ? $attr['params'] : null;
        $base64 = isset($attr['base64']) ? $attr['base64'] : 0;

        if (is_array($params)) {
            $params = array_merge($params, array(
                "_user" => 2,
                "_sistema" =>  'Mercurio', //Core::getInstanceName(),
                "_env" => $this->app->mode,
                "_base64" => ($base64 == 1)
            ));
        } else {
            if (is_null($params) == false) $metodo .= '/' . $params;
            $params = array(
                "_user" => 2,
                "_sistema" =>  'Mercurio', //Core::getInstanceName(),
                "_env" => $this->app->mode,
                "_base64" => ($base64 == 1)
            );
        }

        $userApi = $this->db->fetchOne("SELECT * FROM gener02 WHERE usuario='2'");
        $basicAuth = new BasicAuth('2', $userApi['clave']);

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException("Error no es valido el metodo de acceso API ", 501);
        }

        if ($this->app->mode == "development") {
            $hostConnection = "{$this->app->host_dev}/";
        } else {
            // $basicAuth->encript($this->app->encryption, $this->app->portal_clave);
            $hostConnection = "{$this->app->host_pro}/";
        }

        $url = mapper_sisu_api($servicio) . "/{$metodo}";
        $this->lineaComando =  $hostConnection . "\n" . $url . "\n" . json_encode($params);

        $api = new APIClient($basicAuth, $hostConnection,  $url);
        $api->setTypeJson(true);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        return $this;
    }
}
