@php
    use App\Services\Ecommerce\EstadoPrecompra;

    $badgeEstado = [
        EstadoPrecompra::PENDIENTE => 'bg-warning text-dark',
        EstadoPrecompra::PAGADO => 'bg-success',
        EstadoPrecompra::DESESTIMADO => 'badge-estado-desestimado',
        EstadoPrecompra::RECHAZADO => 'bg-danger',
        EstadoPrecompra::ABANDONADA => 'bg-dark',
    ];

    $motivo = '';
    if ($precompra->motivo_desestimacion != '') {
        if ($precompra->motivo_desestimacion === EstadoPrecompra::MOTIVO_ABANDONO_CHECKOUT) {
            $motivo = 'Abandono del checkout ePayco';
        } elseif ($precompra->motivo_desestimacion === EstadoPrecompra::MOTIVO_ABANDONO_INACTIVIDAD) {
            $motivo = 'Abandono por inactividad';
        } else {
            $motivo = EstadoPrecompra::MOTIVOS_DESESTIMACION[$precompra->motivo_desestimacion] ?? $precompra->motivo_desestimacion;
        }
        if ($precompra->detalle_desestimacion != '') {
            $motivo .= ' — ' . $precompra->detalle_desestimacion;
        }
    }

    $origenLabel = [
        'validacion' => 'Validación API',
        'webhook' => 'Webhook confirmation',
        'manual' => 'Manual',
    ];
@endphp

<div class="admservicios-detalle">
    <div class="mb-3">
        <h6 class="text-uppercase text-muted mb-2">Precompra #{{ $precompra->id }}</h6>
        <div class="row g-2 small">
            <div class="col-md-4">
                <div class="text-muted">Documento</div>
                <div class="fw-semibold">{{ $precompra->documento }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Beneficiario</div>
                <div class="fw-semibold">{{ $precompra->codben ?: '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Estado</div>
                <div>
                    <span class="badge {{ $badgeEstado[$precompra->estado] ?? 'bg-light text-dark' }}">
                        {{ $precompra->estado_descripcion }}
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Servicio</div>
                <div class="fw-semibold">{{ $precompra->codser }} — {{ $precompra->numero }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Valor</div>
                <div class="fw-semibold">${{ number_format((float) $precompra->valor, 0, ',', '.') }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Referencia ePayco</div>
                <div class="fw-semibold"><code>{{ $precompra->ref_payco ?: '—' }}</code></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Fecha precompra</div>
                <div>{{ optional($precompra->fecha_precompra)->format('Y-m-d H:i:s') ?: '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Fecha pago</div>
                <div>{{ optional($precompra->fecha_pago)->format('Y-m-d H:i:s') ?: '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted">Código / motivo ePayco</div>
                <div>{{ $precompra->cod_estado_epayco ?: '—' }} {{ $precompra->motivo_epayco ? '— '.$precompra->motivo_epayco : '' }}</div>
            </div>
            @if ($motivo !== '')
                <div class="col-12">
                    <div class="text-muted">Motivo desestimación / abandono</div>
                    <div>{{ $motivo }}</div>
                    @if ($precompra->fecha_desestimacion)
                        <div class="text-muted mt-1">Fecha: {{ $precompra->fecha_desestimacion->format('Y-m-d H:i:s') }}</div>
                    @endif
                </div>
            @endif
            @if ($precompra->nota)
                <div class="col-12">
                    <div class="text-muted">Nota</div>
                    <div>{{ $precompra->nota }}</div>
                </div>
            @endif
        </div>
    </div>

    <hr class="my-3">

    <h6 class="text-uppercase text-muted mb-2">
        Transacciones ePayco
        <span class="badge bg-light text-dark">{{ $precompra->transaccionesEpayco->count() }}</span>
    </h6>

    @forelse ($precompra->transaccionesEpayco as $tx)
        <div class="border rounded p-3 mb-2 bg-white">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                <div>
                    <span class="badge bg-info text-dark">{{ $origenLabel[$tx->origen] ?? $tx->origen }}</span>
                    <span class="text-muted small ms-1">#{{ $tx->id }}</span>
                </div>
                <div class="small text-muted">
                    {{ optional($tx->created_at)->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div class="row g-2 small">
                <div class="col-md-3">
                    <div class="text-muted">ref_payco</div>
                    <code>{{ $tx->ref_payco ?: '—' }}</code>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">transaction_id</div>
                    <code>{{ $tx->transaction_id ?: '—' }}</code>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">approval_code</div>
                    <code>{{ $tx->approval_code ?: '—' }}</code>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">invoice</div>
                    <code>{{ $tx->invoice ?: '—' }}</code>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Estado ePayco</div>
                    <div>{{ $tx->cod_estado ?: '—' }} {{ $tx->respuesta ? '— '.$tx->respuesta : '' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Motivo</div>
                    <div>{{ $tx->motivo ?: '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Monto</div>
                    <div>
                        @if ($tx->amount !== null)
                            ${{ number_format((float) $tx->amount, 2, ',', '.') }}
                            {{ $tx->currency }}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Fecha ePayco</div>
                    <div>{{ $tx->fecha_epayco ?: '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Banco</div>
                    <div>{{ $tx->bank_name ?: '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Franquicia</div>
                    <div>{{ $tx->franchise ?: '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Tarjeta</div>
                    <div>{{ $tx->card_mask ?: '—' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted">Cuotas</div>
                    <div>{{ $tx->quotas ?: '—' }}</div>
                </div>
            </div>

            @if (! empty($tx->payload_json))
                <details class="mt-2">
                    <summary class="small text-primary" style="cursor:pointer">Ver payload JSON</summary>
                    <pre class="small bg-light border rounded p-2 mt-2 mb-0" style="max-height:240px;overflow:auto">{{ json_encode($tx->payload_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
            @endif
        </div>
    @empty
        <div class="alert alert-light border mb-0">
            No hay transacciones ePayco registradas para esta precompra.
        </div>
    @endforelse
</div>
