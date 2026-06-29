<?php

namespace App\Support;

use Carbon\Carbon;

class DiasHabilesCalculator
{
    /**
     * Dias habiles entre dos fechas (lun-vie, sin festivos).
     * Si $fin es null, usa la fecha actual.
     * Si $ini es null, devuelve null.
     * Si $ini > $fin, devuelve 0.
     */
    public static function between(?string $ini, ?string $fin = null): ?int
    {
        if ($ini === null || $ini === '') {
            return null;
        }

        try {
            $inicio = Carbon::parse($ini)->startOfDay();
            $cierre = $fin !== null && $fin !== ''
                ? Carbon::parse($fin)->startOfDay()
                : Carbon::today()->startOfDay();
        } catch (\Throwable) {
            return null;
        }

        if ($inicio->gt($cierre)) {
            return 0;
        }

        $dias = 0;
        $cursor = $inicio->copy();

        while ($cursor->lte($cierre)) {
            if (! $cursor->isWeekend()) {
                $dias++;
            }
            $cursor->addDay();
        }

        return max(0, $dias - 1);
    }
}
