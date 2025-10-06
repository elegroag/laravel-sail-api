<?php

namespace App\Library\Auth;

use App\Models\Gener02;
use App\Services\Srequest;

class SessionCajas
{
    public function authenticate(Srequest $request)
    {
        if (
            empty($request->getParam("tipfun")) ||
            empty($request->getParam("usuario")) ||
            empty($request->getParam("estado"))
        ) {
            return false;
        }

        $condiciones = [
            "tipfun" => $request->getParam("tipfun"),
            "usuario" => $request->getParam("usuario"),
            "estado" => $request->getParam("estado")
        ];

        if (!empty($request->getParam("estado"))) {
            $condiciones['estado'] = $request->getParam("estado");
        }
        $usuario = Gener02::where($condiciones)->first();
        if (!$usuario) {
            return false;
        }

        session()->put('tipfun', $request->getParam("tipfun"));
        return [
            'cedtra' => $usuario->cedtra,
            'usuario' => $usuario->usuario,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'estado' => $usuario->estado,
            'ts' => time(),
        ];
    }
}
