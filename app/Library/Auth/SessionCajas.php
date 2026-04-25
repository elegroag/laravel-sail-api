<?php

namespace App\Library\Auth;

use App\Models\Gener02;

class SessionCajas implements SessionInterface
{
    public function authenticate(array $request): array|false
    {
        if (
            empty($request['tipfun']) ||
            empty($request['usuario']) ||
            empty($request['estado'])
        ) {
            return false;
        }

        $condiciones = [
            'tipfun' => $request['tipfun'],
            'usuario' => $request['usuario'],
            'estado' => $request['estado'],
        ];

        if (! empty($request['estado'])) {
            $condiciones['estado'] = $request['estado'];
        }
        $usuario = Gener02::where($condiciones)->first();
        if (! $usuario) {
            return false;
        }

        session([
            'tipfun' => $request['tipfun'],
            'usuario' => $request['usuario']
        ]);

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
