<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpaycoTransaccion extends Model
{
    protected $table = 'epayco_transacciones';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'precompra_id',
        'ref_payco',
        'transaction_id',
        'invoice',
        'approval_code',
        'cod_estado',
        'respuesta',
        'motivo',
        'amount',
        'currency',
        'bank_name',
        'franchise',
        'card_mask',
        'quotas',
        'signature',
        'fecha_epayco',
        'origen',
        'payload_json',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payload_json' => 'array',
    ];

    public function precompra(): BelongsTo
    {
        return $this->belongsTo(PrecompraServicio::class, 'precompra_id');
    }

    /**
     * Persiste un snapshot de la respuesta normalizada de ApiEpayco::validarReferenciaApify().
     *
     * @param  array<string, mixed>  $datosPago
     */
    public static function registrarDesdeValidacion(?int $precompraId, array $datosPago, string $origen = 'validacion'): self
    {
        $codEstado = $datosPago['cod_estado'] ?? null;
        $motivo = (string) ($datosPago['motivo'] ?: ($datosPago['respuesta'] ?? ''));
        $amount = $datosPago['monto'] ?? null;

        return self::create([
            'precompra_id' => $precompraId,
            'ref_payco' => $datosPago['ref_payco'] ?? null,
            'transaction_id' => $datosPago['x_transaction_id'] ?? null,
            'invoice' => $datosPago['x_id_invoice'] ?? null,
            'approval_code' => $datosPago['x_approval_code'] ?? null,
            'cod_estado' => $codEstado !== null && $codEstado !== '' ? (string) $codEstado : null,
            'respuesta' => isset($datosPago['respuesta']) ? mb_substr((string) $datosPago['respuesta'], 0, 100) : null,
            'motivo' => $motivo !== '' ? mb_substr($motivo, 0, 255) : null,
            'amount' => is_numeric($amount) ? $amount : null,
            'currency' => $datosPago['x_currency_code'] ?? null,
            'bank_name' => $datosPago['x_bank_name'] ?? null,
            'franchise' => $datosPago['x_franchise'] ?? null,
            'card_mask' => $datosPago['x_card_number'] ?? null,
            'quotas' => isset($datosPago['x_quotas']) ? (string) $datosPago['x_quotas'] : null,
            'signature' => $datosPago['x_signature'] ?? null,
            'fecha_epayco' => $datosPago['x_date'] ?? null,
            'origen' => $origen,
            'payload_json' => $datosPago['payload_raw'] ?? null,
        ]);
    }
}
