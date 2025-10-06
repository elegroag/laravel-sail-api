<?php

namespace App\Library\Auth;

use App\Models\Mercurio07;
use App\Services\Srequest;

class SessionMercurio
{
    public function authenticate(Srequest $request)
    {
        // Validaciones básicas de parámetros
        if (
            empty($request->getParam("tipo")) ||
            empty($request->getParam("coddoc")) ||
            empty($request->getParam("documento"))
        ) {
            return false;
        }

        $condiciones = [
            "tipo" => $request->getParam("tipo"),
            "coddoc" => $request->getParam("coddoc"),
            "documento" => $request->getParam("documento")
        ];

        if (!empty($request->getParam("estado"))) {
            $condiciones['estado'] = $request->getParam("estado");
        }
        $usuario = Mercurio07::where($condiciones)->first();
        if (!$usuario) {
            return false;
        }

        session()->put('tipo', $request->getParam("tipo"));
        session()->put('estado_afiliado', $request->getParam("estado_afiliado"));
        return [
            'documento' => $usuario->documento,
            'coddoc' => $usuario->coddoc,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'codciu' => $usuario->codciu,
            'estado' => $usuario->estado,
            'ts' => time(),
        ];
    }
}
