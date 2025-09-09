<?php

namespace App\Services\Utils;

use App\Models\Mercurio20;
use Carbon\Carbon;

class Logger
{
    public function registrarLog($tx, $accion, $nota = "")
    {
        $user = session()->get('user');
        $tipo = session()->get('tipo');
        $coddoc = $user['coddoc'];
        $documento = $user['documento'];

        $today = Carbon::now();
        $mercurio20 = new Mercurio20();
        $mercurio20->setTipo($tipo);
        $mercurio20->setCoddoc($coddoc);
        $mercurio20->setDocumento($documento);
        $mercurio20->setIp($_SERVER["REMOTE_ADDR"]);
        $mercurio20->setFecha($today->format('Y-m-d'));
        $mercurio20->setHora(date("H:i"));
        $mercurio20->setAccion($accion);
        $mercurio20->setNota($nota);
        $mercurio20->save();
        return $mercurio20->getLog();
    }

    public function getLog($log)
    {
        return Mercurio20::where('log', $log)->first();
    }
}
