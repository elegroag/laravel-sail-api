<?php

namespace App\Services\Ecommerce;

use App\Models\EpaycoTransaccion;
use App\Models\PrecompraServicio;
use App\Services\Api\ApiSubsidio;
use Illuminate\Support\Facades\Log;

/**
 * Procesa el webhook confirmation de ePayco: firma, auditoría, estado de precompra
 * y registro de venta en subsidio cuando el pago es aceptado.
 */
class EpaycoConfirmationService
{
    public function __construct(
        protected EpaycoSignatureValidator $signatureValidator,
        protected ?ApiSubsidio $apiSubsidio = null,
    ) {
        $this->apiSubsidio = $apiSubsidio ?? new ApiSubsidio;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: bool, http: int, message: string, data?: array<string, mixed>}
     */
    public function handle(array $payload): array
    {
        if (! $this->signatureValidator->credentialsConfigured()) {
            Log::error('ePayco confirmation: faltan EPAYCO_CUSTOMER_ID / EPAYCO_P_KEY');

            return [
                'ok' => false,
                'http' => 503,
                'message' => 'Credenciales de firma ePayco no configuradas',
            ];
        }

        if (! $this->signatureValidator->isValid($payload)) {
            Log::warning('ePayco confirmation: firma invalida', [
                'ref_payco' => $payload['x_ref_payco'] ?? null,
            ]);

            return [
                'ok' => false,
                'http' => 400,
                'message' => 'Invalid signature',
            ];
        }

        $datosPago = $this->normalizarPayload($payload);
        $precompra = $this->resolverPrecompra($payload, $datosPago);
        $yaPagada = $precompra?->isPagado() ?? false;

        EpaycoTransaccion::registrarDesdeValidacion(
            $precompra?->id,
            $datosPago,
            'webhook'
        );

        if ($precompra) {
            $this->actualizarPrecompra($precompra, $datosPago);
            $precompra->refresh();
        }

        $aprobado = ($datosPago['aprobado'] ?? false) === true
            && (int) ($datosPago['cod_estado'] ?? 0) === 1;

        $ventaRegistrada = false;
        if ($aprobado && $precompra && ! $yaPagada) {
            $ventaRegistrada = $this->registrarVentaSubsidio($precompra, $datosPago);
        }

        Log::info('ePayco confirmation procesado', [
            'ref_payco' => $datosPago['ref_payco'] ?? null,
            'cod_estado' => $datosPago['cod_estado'] ?? null,
            'precompra_id' => $precompra?->id,
            'ya_pagada' => $yaPagada,
            'venta_registrada' => $ventaRegistrada,
        ]);

        return [
            'ok' => true,
            'http' => 200,
            'message' => 'OK',
            'data' => [
                'precompra_id' => $precompra?->id,
                'estado' => $precompra?->estado,
                'aprobado' => $aprobado,
                'venta_registrada' => $ventaRegistrada,
                'ya_pagada' => $yaPagada,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function normalizarPayload(array $payload): array
    {
        $codEstado = (int) ($payload['x_cod_transaction_state'] ?? $payload['x_cod_respuesta'] ?? 0);

        return [
            'aprobado' => $codEstado === 1,
            'cod_estado' => $codEstado,
            'respuesta' => $payload['x_response'] ?? $payload['x_respuesta'] ?? 'Sin respuesta',
            'motivo' => $payload['x_response_reason_text'] ?? '',
            'monto' => $payload['x_amount'] ?? '0',
            'ref_payco' => $payload['x_ref_payco'] ?? $payload['ref_payco'] ?? null,
            'x_id_invoice' => $payload['x_id_invoice'] ?? $payload['x_id_factura'] ?? null,
            'x_transaction_id' => $payload['x_transaction_id'] ?? null,
            'x_approval_code' => $payload['x_approval_code'] ?? $payload['x_approval_código'] ?? null,
            'x_bank_name' => $payload['x_bank_name'] ?? $payload['x_nombre_banco'] ?? null,
            'x_franchise' => $payload['x_franchise'] ?? null,
            'x_card_number' => $payload['x_card_number'] ?? null,
            'x_quotas' => $payload['x_quotas'] ?? $payload['x_cuotas'] ?? null,
            'x_currency_code' => $payload['x_currency_code'] ?? $payload['código_moneda_x'] ?? null,
            'x_date' => $payload['x_transaction_date'] ?? $payload['x_fecha_transaccion'] ?? $payload['x_date'] ?? null,
            'x_signature' => $payload['x_signature'] ?? null,
            'payload_raw' => $payload,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $datosPago
     */
    protected function resolverPrecompra(array $payload, array $datosPago): ?PrecompraServicio
    {
        $refPayco = (string) ($datosPago['ref_payco'] ?? '');
        if ($refPayco !== '') {
            $byRef = PrecompraServicio::where('ref_payco', $refPayco)->first();
            if ($byRef) {
                return $byRef;
            }
        }

        $precompraId = (int) ($payload['x_extra4'] ?? $payload['extra4'] ?? 0);
        if ($precompraId > 0) {
            $byId = PrecompraServicio::where('id', $precompraId)->first();
            if ($byId) {
                return $byId;
            }
        }

        $documento = (string) ($payload['x_extra1'] ?? $payload['extra1'] ?? '');
        $codser = (string) ($payload['x_extra2'] ?? $payload['extra2'] ?? '');
        $numero = (int) ($payload['x_extra3'] ?? $payload['extra3'] ?? 0);

        if ($documento !== '' && $codser !== '' && $numero > 0) {
            return PrecompraServicio::query()
                ->where('documento', $documento)
                ->where('codser', $codser)
                ->where('numero', $numero)
                ->whereIn('estado', [EstadoPrecompra::PENDIENTE, EstadoPrecompra::ABANDONADA])
                ->orderByDesc('id')
                ->first();
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $datosPago
     */
    protected function actualizarPrecompra(PrecompraServicio $precompra, array $datosPago): void
    {
        if ($precompra->isPagado()) {
            return;
        }

        // No reabrir desestimadas manualmente
        if ($precompra->isDesestimado()) {
            return;
        }

        $codEstado = (int) ($datosPago['cod_estado'] ?? 0);
        $nuevoEstado = EstadoPrecompra::desdeCodigoEpayco($codEstado);
        $refPayco = (string) ($datosPago['ref_payco'] ?? '');

        $precompra->fill([
            'estado' => $nuevoEstado,
            'ref_payco' => $refPayco !== '' ? $refPayco : $precompra->ref_payco,
            'cod_estado_epayco' => (string) $codEstado,
            'motivo_epayco' => mb_substr((string) ($datosPago['motivo'] ?: ($datosPago['respuesta'] ?? '')), 0, 255),
        ]);

        if ($nuevoEstado === EstadoPrecompra::PAGADO && ! $precompra->fecha_pago) {
            $precompra->fecha_pago = now();
        }

        $precompra->save();
    }

    /**
     * @param  array<string, mixed>  $datosPago
     */
    protected function registrarVentaSubsidio(PrecompraServicio $precompra, array $datosPago): bool
    {
        try {
            $refpago = (string) ($datosPago['ref_payco'] ?? $precompra->ref_payco ?? '');
            if ($refpago === '') {
                return false;
            }

            $this->apiSubsidio->send([
                'servicio' => 'Movil',
                'metodo' => 'guardar-venta',
                'params' => [
                    'cedtra' => $precompra->documento,
                    'codser' => $precompra->codser,
                    'numero' => $precompra->numero,
                    'refpago' => $refpago,
                    'nota' => $precompra->nota ?? '',
                    'codben' => $precompra->codben ?: $precompra->documento,
                ],
            ]);

            $resultado = $this->apiSubsidio->toArray();
            $ok = (bool) ($resultado['flag'] ?? false);

            if (! $ok) {
                Log::warning('ePayco confirmation: guardar-venta no OK', [
                    'precompra_id' => $precompra->id,
                    'message' => $resultado['message'] ?? null,
                ]);
            }

            return $ok;
        } catch (\Throwable $e) {
            Log::error('ePayco confirmation: error guardar-venta', [
                'precompra_id' => $precompra->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
