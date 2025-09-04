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
        $mercurio05 = (new Mercurio05())->findFirst("codciu='{$codciu}'");
        if ($mercurio05 == false) {
            $mercurio04 = (new Mercurio04())->findFirst("principal='S'");
            $codofi = $mercurio04->getCodofi();
        } else {
            $codofi = $mercurio05->getCodofi();
        }
        $mercurio08 = (new Mercurio08())->findFirst("codofi = '{$codofi}' and tipopc='{$tipopc}' and orden='1'");
        if ($mercurio08 == false) {
            $usuario = (new Mercurio08())->minimum("usuario", "conditions: codofi = '{$codofi}' and tipopc='{$tipopc}' ");
        } else {
            $usuario = $mercurio08->getUsuario();
        }

        if ($usuario == "") return "";
        $usuario_orden = (new Mercurio08())->minimum(
            "usuario",
            "conditions: codofi = '{$codofi}' and tipopc='{$tipopc}' and usuario > $usuario"
        );

        Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->update(['orden' => '0']);

        Mercurio08::where('codofi', $codofi)
            ->where('tipopc', $tipopc)
            ->where('usuario', $usuario_orden)
            ->update(['orden' => '1']);

        if (is_null($usuario) || $usuario == '') {
            throw new DebugException("No se puede realizar el registro, no hay funcionarios disponibles, comuniquese con el área de atención al cliente", 503);
        }
        return $usuario;
    }
}
