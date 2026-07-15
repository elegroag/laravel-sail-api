<?php

namespace App\Services\Ecommerce;

/**
 * Estados de una precompra de servicio y su mapeo
 * desde los codigos de transaccion de ePayco (x_cod_transaction_state).
 *
 * Codigos ePayco:
 *   1 = Aceptada, 2 = Rechazada, 3 = Pendiente, 4 = Fallida,
 *   6 = Reversada, 7 = Retenida, 8 = Iniciada, 9 = Expirada,
 *   10 = Abandonada, 11 = Cancelada, 12 = Antifraude
 */
class EstadoPrecompra
{
    public const PENDIENTE = 'PE';

    public const PAGADO = 'PA';

    public const DESESTIMADO = 'DE';

    public const RECHAZADO = 'RE';

    public const DESCRIPCIONES = [
        self::PENDIENTE => 'Pendiente de pago',
        self::PAGADO => 'Pagado',
        self::DESESTIMADO => 'Desestimado',
        self::RECHAZADO => 'Rechazado',
    ];

    public static function desdeCodigoEpayco(int $codigo): string
    {
        return match ($codigo) {
            1 => self::PAGADO,               // Aceptada
            3, 8 => self::PENDIENTE,         // Pendiente / Iniciada
            2, 4, 6, 11, 12 => self::DESESTIMADO, // Rechazada / Fallida / Reversada / Cancelada / Antifraude
            7, 9, 10 => self::RECHAZADO,     // Retenida / Expirada / Abandonada
            default => self::RECHAZADO,
        };
    }

    public static function descripcion(string $estado): string
    {
        return self::DESCRIPCIONES[$estado] ?? 'Desconocido';
    }
}
