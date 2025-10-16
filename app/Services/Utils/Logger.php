<?php

namespace App\Services\Utils;

use App\Models\Mercurio20;
use Carbon\Carbon;

class Logger
{
    public function registrarLog($tx, $accion, $nota = '')
    {
        $user = session()->get('user');
        $tipo = session()->get('tipo');
        $coddoc = $user['coddoc'];
        $documento = $user['documento'];

        $today = Carbon::now();
        $mercurio20 = Mercurio20::create([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'fecha' => $today->format('Y-m-d'),
            'hora' => date('H:i'),
            'accion' => $accion,
            'nota' => $nota,
        ]);
        return $mercurio20->log;
    }

    public function getLog($log)
    {
        return Mercurio20::where('log', $log)->first();
    }
}
