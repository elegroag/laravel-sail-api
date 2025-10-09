<?php

namespace App\Services\Utils;

use App\Exceptions\DebugException;
use App\Models\Mercurio04;
use App\Models\Mercurio05;
use App\Models\Mercurio08;

class AsignarFuncionario
{
    public function asignar($tipopc, $codciu)
    {
        $mercurio05 = Mercurio05::where('codciu', $codciu)->first();

        if ($mercurio05 == false) {
            $mercurio04 = Mercurio04::where('principal', 'S')->first();
            $codofi = $mercurio04->getCodofi();
        } else {
            $codofi = $mercurio05->getCodofi();
        }
        $mercurio08 = Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->where('orden', '1')
            ->first();

        if ($mercurio08 == false) {
            $usuario = Mercurio08::where('codofi', $codofi)->where('tipopc', $tipopc)->min('usuario');
        } else {
            $usuario = $mercurio08->getUsuario();
        }

        if ($usuario == '') {
            return '';
        }

        $usuario_orden = Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->where('usuario', '>', $usuario)
            ->min('usuario');

        Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->update(['orden' => '0']);

        Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->where('usuario', $usuario_orden)
            ->update(['orden' => '1']);

        if (is_null($usuario) || $usuario == '') {
            throw new DebugException('No se puede realizar el registro, no hay funcionarios disponibles, comuniquese con el área de atención al cliente', 503);
        }

        return $usuario;
    }
}
