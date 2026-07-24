<?php

namespace App\Services\Utils;

use App\Models\Mercurio10;

class Mercurio10Cierre
{
    /**
     * Estados de respuesta del asesor que cierran el ciclo.
     *
     * @var array<int, string>
     */
    public const ESTADOS_CIERRE = ['A', 'X', 'D'];

    public static function marcarRespuesta(Mercurio10 $evento): void
    {
        $estado = strtoupper(trim((string) $evento->estado));
        if (! in_array($estado, self::ESTADOS_CIERRE, true)) {
            return;
        }

        if ($evento->cerrada === 'S') {
            return;
        }

        $evento->setCerrada('S');
        $evento->save();
    }

    public static function cerrarPendientes(string $tipopc, int|string $numero): void
    {
        Mercurio10::query()
            ->where('tipopc', $tipopc)
            ->where('numero', $numero)
            ->where('estado', 'P')
            ->where(function ($q) {
                $q->where('cerrada', 'N')->orWhereNull('cerrada');
            })
            ->update(['cerrada' => 'S']);
    }

    public static function aplicarCierreRespuesta(Mercurio10 $evento): void
    {
        $estado = strtoupper(trim((string) $evento->estado));
        if (! in_array($estado, self::ESTADOS_CIERRE, true)) {
            return;
        }

        self::marcarRespuesta($evento);
        self::cerrarPendientes((string) $evento->tipopc, $evento->numero);
    }
}
