<?php

namespace App\Services\Utils;

use App\Exceptions\AuthException;
use App\Models\Mercurio07;
use App\Services\Srequest;

class ChangeCuentaService
{
    /**
     * initializa function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  Srequest  $request
     * @return void
     */
    public function initializa($request)
    {
        $tipo = $request->getParam('tipo');
        $coddoc = $request->getParam('coddoc');
        $documento = $request->getParam('documento');
        $usuario = $request->getParam('usuario');

        $empresa_registrada = (new Mercurio07)->findFirst(" documento='{$documento}' AND tipo='{$tipo}' AND coddoc='{$coddoc}' AND estado='A'");
        if ($empresa_registrada == false) {
            throw new AuthException('La empresa no está disponible para su administración.', 501);
        }

        /* $auth = new Auth('model', "class: Mercurio07", "tipo: {$tipo}", "coddoc: {$coddoc}", "documento: {$documento}", "estado: A");
        if (!$auth->authenticate()) {
            throw new AuthException("Error acceso incorrecto, no se completa la autenticación", 504);
        } else {
            return true;
        } */
    }
}
