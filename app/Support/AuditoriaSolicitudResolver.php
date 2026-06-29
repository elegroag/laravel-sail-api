<?php

namespace App\Support;

use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio35;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;
use App\Models\Mercurio40;
use App\Models\Mercurio41;
use App\Models\Mercurio45;
use App\Models\Mercurio47;
use InvalidArgumentException;

class AuditoriaSolicitudResolver
{
    /**
     * @var array<string, class-string>
     */
    private const MODEL_MAP = [
        '1' => Mercurio31::class,
        '2' => Mercurio30::class,
        '3' => Mercurio32::class,
        '4' => Mercurio34::class,
        '5' => Mercurio47::class,
        '6' => Mercurio47::class,
        '7' => Mercurio35::class,
        '8' => Mercurio45::class,
        '9' => Mercurio38::class,
        '10' => Mercurio36::class,
        '11' => Mercurio39::class,
        '12' => Mercurio40::class,
        '13' => Mercurio41::class,
    ];

    public static function resolve(string $tipopc, int $id): ?object
    {
        $modelClass = self::MODEL_MAP[$tipopc] ?? null;

        if ($modelClass === null) {
            throw new InvalidArgumentException("Tipo de operación no válido: {$tipopc}");
        }

        return $modelClass::where('id', $id)->first();
    }
}
