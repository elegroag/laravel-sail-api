<?php

namespace App\Services\Ecommerce;

/**
 * Valida x_signature de ePayco según la fórmula oficial:
 * sha256(p_cust_id_cliente ^ p_key ^ x_ref_payco ^ x_transaction_id ^ x_amount ^ x_currency_code)
 *
 * @see https://docs.epayco.com/docs/checkout-respuesta-y-confirmacion
 */
class EpaycoSignatureValidator
{
    public function __construct(
        protected ?string $customerId = null,
        protected ?string $pKey = null,
    ) {
        $this->customerId = $customerId ?? (string) config('app.epayco.customer_id', '');
        $this->pKey = $pKey ?? (string) config('app.epayco.p_key', '');
    }

    public function credentialsConfigured(): bool
    {
        return $this->customerId !== '' && $this->pKey !== '';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function expectedSignature(array $data): string
    {
        $string = $this->customerId.'^'
            .$this->pKey.'^'
            .(string) ($data['x_ref_payco'] ?? '').'^'
            .(string) ($data['x_transaction_id'] ?? '').'^'
            .(string) ($data['x_amount'] ?? '').'^'
            .(string) ($data['x_currency_code'] ?? '');

        return hash('sha256', $string);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function isValid(array $data): bool
    {
        if (! $this->credentialsConfigured()) {
            return false;
        }

        $received = (string) ($data['x_signature'] ?? '');
        if ($received === '') {
            return false;
        }

        return hash_equals($this->expectedSignature($data), $received);
    }
}
