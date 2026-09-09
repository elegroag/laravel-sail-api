<?php

namespace App\Services\Ecommerce;

use App\Exceptions\DebugException;
use App\Models\EpaycoCuenta;

/**
 * Resuelve credenciales ePayco desde epayco_cuentas por P_CUST_ID_CLIENTE.
 */
class EpaycoCuentaResolver
{
    /**
     * @throws DebugException
     */
    public function findByPIdCustomer(string $pIdCustomer): EpaycoCuenta
    {
        $pIdCustomer = trim($pIdCustomer);
        if ($pIdCustomer === '') {
            throw new DebugException('El identificador ePayco del servicio es requerido.');
        }

        $cuenta = EpaycoCuenta::query()
            ->where('p_id_customer', $pIdCustomer)
            ->first();

        if (! $cuenta) {
            throw new DebugException(
                'No hay cuenta ePayco configurada para p_id_customer: '.$pIdCustomer
            );
        }

        return $cuenta;
    }
}
