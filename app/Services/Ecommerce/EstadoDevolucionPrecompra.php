<?php

namespace App\Services\Ecommerce;

/**
 * Estados de la solicitud formal de devolución (tabla devolucion_precompra).
 */
class EstadoDevolucionPrecompra
{
    public const SOLICITADA = 'SOLICITADA';

    public const EN_TRAMITE = 'EN_TRAMITE';

    public const APROBADO = 'APROBADO';

    public const RECHAZADA = 'RECHAZADA';

    public const DESCRIPCIONES = [
        self::SOLICITADA => 'Solicitada',
        self::EN_TRAMITE => 'En trámite',
        self::APROBADO => 'Aprobado',
        self::RECHAZADA => 'Rechazada',
    ];

    public static function descripcion(string $estado): string
    {
        return self::DESCRIPCIONES[$estado] ?? 'Desconocido';
    }
}
