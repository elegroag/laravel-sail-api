<?php

namespace App\Library\Auth;

use App\Models\Mercurio07;

class SessionMercurio implements SessionInterface
{
    public function authenticate(array $request): array|false
    {
        // Validaciones básicas de parámetros
        if (
            empty($request['tipo']) ||
            empty($request['coddoc']) ||
            empty($request['documento'])
        ) {
            return false;
        }

        $condiciones = [
            'tipo' => $request['tipo'],
            'coddoc' => $request['coddoc'],
            'documento' => $request['documento'],
        ];

        if (! empty($request['estado'])) {
            $condiciones['estado'] = $request['estado'];
        }
        $usuario = Mercurio07::where($condiciones)->first();
        if (! $usuario) {
            return false;
        }

        session([
            'tipo' => $request['tipo'],
            'estado_afiliado' => $request['estado_afiliado']
        ]);

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
