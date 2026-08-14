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

    public const ABANDONADA = 'AB';

    public const DESCRIPCIONES = [
        self::PENDIENTE => 'Pendiente de pago',
        self::PAGADO => 'Pagado',
        self::DESESTIMADO => 'Desestimado',
        self::RECHAZADO => 'Rechazado',
        self::ABANDONADA => 'Abandonada',
    ];

    /**
     * Motivos disponibles para desestimar una precompra pendiente.
     * La clave OTRO habilita un campo de texto libre en el frontend.
     */
    public const MOTIVO_OTRO = 'OTRO';

    public const MOTIVO_ABANDONO_CHECKOUT = 'ABANDONO_CHECKOUT';

    public const MOTIVO_ABANDONO_INACTIVIDAD = 'ABANDONO_INACTIVIDAD';

    public const MOTIVOS_DESESTIMACION = [
        'YA_NO_INTERESA' => 'Ya no estoy interesado en el servicio',
        'VALOR_ALTO' => 'El valor del servicio es muy alto',
        'COMPRA_OTRO_MEDIO' => 'Realicé la compra por otro medio',
        'PROBLEMA_PAGO' => 'Tuve problemas con el medio de pago',
        self::MOTIVO_OTRO => 'Otro motivo',
    ];

    /**
     * El estado DESESTIMADO nunca se asigna automaticamente: queda reservado
     * para cuando el usuario desestima la compra de forma manual con un motivo.
     *
     * El estado ABANDONADA se asigna cuando el usuario cierra el checkout de
     * ePayco sin completar el pago (onClose), o cuando el job de limpieza
     * marca precompras PE antiguas por inactividad. Queda cerrada y no es retomable.
     *
     * Mientras el pago no se complete ni sea rechazado por la pasarela,
     * la precompra permanece PENDIENTE para que el usuario pueda retomarla.
     */
    public static function desdeCodigoEpayco(int $codigo): string
    {
        return match ($codigo) {
            1 => self::PAGADO,             // Aceptada
            2, 4, 6, 7, 12 => self::RECHAZADO, // Rechazada / Fallida / Reversada / Retenida / Antifraude
            default => self::PENDIENTE,    // Pendiente / Iniciada / Expirada / Abandonada / Cancelada
        };
    }

    public static function descripcion(string $estado): string
    {
        return self::DESCRIPCIONES[$estado] ?? 'Desconocido';
    }
}
