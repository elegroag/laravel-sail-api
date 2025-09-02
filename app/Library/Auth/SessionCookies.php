<?php
namespace App\Library\Auth;

class SessionCookies
{
    private $model, $tipo, $coddoc, $documento, $estado;

    public function __construct(...$params)
    {
        $arguments = get_params_destructures($params);
        $this->model = $arguments['model'];
        $this->tipo = $arguments['tipo'];
        $this->coddoc = $arguments['coddoc'];
        $this->documento = $arguments['documento'];
        $this->estado = $arguments['estado'];
    }

    public function init()
    {
    }

    /**
     * authenticate function
     * @return bool
     */
    public function authenticate()
    {
        return true;
    }

    public static function destroyIdentity()
    {
    }
}
