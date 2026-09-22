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
            $motivo .= ' — '.$precompra->detalle_desestimacion;
        }
    }

    $origenLabel = [
        'validacion' => 'Validación API',
        'webhook' => 'Webhook confirmation',
        'manual' => 'Manual',
    ];

    $codbens = $precompra->codbenList();
    $estadoBadgeClass = $badgeEstado[$precompra->estado] ?? 'bg-light text-dark';

    $registrada = ($ventaSubsidio['registrada'] ?? false) === true;
    $consultado = ($ventaSubsidio['consultado'] ?? false) === true;
    $venta = ($registrada && is_array($ventaSubsidio['data'] ?? null)) ? $ventaSubsidio['data'] : null;
@endphp

<div class="admservicios-detalle p-1">
    <h6 class="heading-small text-muted mb-3">
        Resumen de la precompra
        <span class="badge {{ $estadoBadgeClass }} ms-1">{{ $precompra->estado_descripcion }}</span>
    </h6>
    <div class="row g-2">
        <div class="col-md-6">
            <ul class="list-group list-group-flush border rounded mb-0 h-100">
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Id</span>
                    <span class="text-end fw-semibold">{{ $precompra->id }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Documento titular</span>
                    <span class="text-end fw-semibold">{{ $precompra->documento }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Beneficiario(s)</span>
                    <span class="text-end fw-semibold">{{ $codbens !== [] ? implode(', ', $codbens) : '—' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Servicio / apertura</span>
                    <span class="text-end fw-semibold">{{ $precompra->codser }} — {{ $precompra->numero }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Valor</span>
                    <span class="text-end fw-semibold">${{ number_format((float) $precompra->valor, 0, ',', '.') }}</span>
                </li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="list-group list-group-flush border rounded mb-0 h-100">
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Referencia ePayco</span>
                    <span class="text-end"><code>{{ $precompra->ref_payco ?: '—' }}</code></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Fecha precompra</span>
                    <span class="text-end">{{ optional($precompra->fecha_precompra)->format('Y-m-d H:i:s') ?: '—' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Fecha pago</span>
                    <span class="text-end">{{ optional($precompra->fecha_pago)->format('Y-m-d H:i:s') ?: '—' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                    <span class="text-muted">Código / motivo ePayco</span>
                    <span class="text-end">
                        {{ $precompra->cod_estado_epayco ?: '—' }}
                        @if ($precompra->motivo_epayco)
                            — {{ $precompra->motivo_epayco }}
                        @endif
                    </span>
                </li>
            </ul>
        </div>
        @if ($motivo !== '')
            <div class="col-12">
                <ul class="list-group list-group-flush border rounded mb-0">
                    <li class="list-group-item py-2">
                        <div class="text-muted mb-1">Motivo desestimación / abandono</div>
                        <div>{{ $motivo }}</div>
                        @if ($precompra->fecha_desestimacion)
                            <div class="text-muted small mt-1">
                                Fecha: {{ $precompra->fecha_desestimacion->format('Y-m-d H:i:s') }}
                            </div>
                        @endif
                    </li>
                </ul>
            </div>
        @endif
        @if ($precompra->nota)
            <div class="col-12">
                <ul class="list-group list-group-flush border rounded mb-0">
                    <li class="list-group-item py-2">
                        <div class="text-muted mb-1">Nota</div>
                        <div>{{ $precompra->nota }}</div>
                    </li>
                </ul>
            </div>
        @endif
    </div>

    <h6 class="heading-small text-muted mb-3 mt-4">
        Registro en Subsidio
        @if ($registrada)
            <span class="badge bg-success ms-1">Registrada</span>
        @elseif ($consultado)
            <span class="badge bg-warning text-dark ms-1">No registrada</span>
        @else
            <span class="badge bg-secondary ms-1">Sin consultar</span>
        @endif
    </h6>

    @if ($venta !== null)
        <ul class="list-group list-group-flush border rounded mb-0">
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Marca / documento</span>
                <span class="text-end fw-semibold">{{ ($venta['marca'] ?? '—') }}-{{ ($venta['documento'] ?? '—') }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Fecha / hora</span>
                <span class="text-end">{{ ($venta['fecha'] ?? '—') }} {{ $venta['hora'] ?? '' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Estado</span>
                <span class="text-end">{{ $venta['estado_texto'] ?? ($venta['estado'] ?? '—') }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Ref. pago</span>
                <span class="text-end"><code>{{ $venta['refpago'] ?? '—' }}</code></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Titular</span>
                <span class="text-end fw-semibold">{{ $venta['cedtra_titular'] ?? '—' }} — {{ $venta['nombre_titular'] ?? '' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Valor pago</span>
                <span class="text-end fw-semibold">${{ number_format((float) ($venta['valpago'] ?? 0), 0, ',', '.') }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Valor subsidio</span>
                <span class="text-end">${{ number_format((float) ($venta['valsub'] ?? 0), 0, ',', '.') }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                <span class="text-muted">Forma de pago</span>
                <span class="text-end">{{ $venta['forma_pago_detalle'] ?? '—' }} ({{ $venta['codfor'] ?? '—' }})</span>
            </li>
            @if (! empty($venta['nota']))
                <li class="list-group-item py-2">
                    <div class="text-muted mb-1">Nota</div>
                    <div>{{ $venta['nota'] }}</div>
                </li>
            @endif
        </ul>

        @if (! empty($venta['items']) && is_array($venta['items']))
            <h6 class="heading-small text-muted mb-3 mt-3">Ítems de la venta</h6>
            <ul class="list-group border rounded mb-0">
                @foreach ($venta['items'] as $item)
                    <li class="list-group-item py-2">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <span class="fw-semibold">
                                {{ $item['codben'] ?? '—' }} — {{ $item['nombre_beneficiario'] ?? '' }}
                            </span>
                            <span class="badge bg-light text-dark">Sec {{ $item['sec'] ?? '—' }}</span>
                        </div>
                        <ul class="list-unstyled small mb-0 text-muted">
                            <li>Tipo: {{ $item['tipben_texto'] ?? ($item['tipben'] ?? '—') }}</li>
                            <li>Servicio: {{ $item['codser'] ?? '' }} / {{ $item['numero'] ?? '' }} {{ $item['nombre_servicio'] ?? '' }}</li>
                            <li>
                                Valor: ${{ number_format((float) ($item['valser'] ?? 0), 0, ',', '.') }}
                                · Subsidio: ${{ number_format((float) ($item['valsub'] ?? 0), 0, ',', '.') }}
                            </li>
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    @else
        <ul class="list-group list-group-flush border rounded mb-0">
            <li class="list-group-item py-2">
                <div class="text-muted mb-1">Estado consulta</div>
                <div>{{ $ventaSubsidio['msg'] ?? 'Sin información de registro en Subsidio.' }}</div>
            </li>
        </ul>

        @if (! empty($puedeRegistrarSubsidio))
            <div class="mt-3">
                <button
                    type="button"
                    class="btn btn-sm btn-primary"
                    data-toggle="registrar-subsidio"
                    data-id="{{ $precompra->id }}"
                >
                    <i class="fas fa-file-invoice-dollar me-1"></i>
                    Registrar compra en Subsidio
                </button>
                <div class="form-text">
                    Valida el pago en ePayco y, si está aprobado, registra la compra en Subsidio.
                </div>
                <div id="resultado_validacion_epayco" class="mt-2" aria-live="polite"></div>
            </div>
        @endif
    @endif

    <h6 class="heading-small text-muted mb-3 mt-4">
        Transacciones ePayco
        <span class="badge bg-light text-dark ms-1">{{ $precompra->transaccionesEpayco->count() }}</span>
    </h6>

    @forelse ($precompra->transaccionesEpayco as $tx)
        <div class="mb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div>
                    <span class="badge bg-info text-dark">{{ $origenLabel[$tx->origen] ?? $tx->origen }}</span>
                    <span class="text-muted small ms-1">#{{ $tx->id }}</span>
                </div>
                <div class="small text-muted">
                    {{ optional($tx->created_at)->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush border rounded mb-0 h-100">
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">ref_payco</span>
                            <span class="text-end"><code>{{ $tx->ref_payco ?: '—' }}</code></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">transaction_id</span>
                            <span class="text-end"><code>{{ $tx->transaction_id ?: '—' }}</code></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">approval_code</span>
                            <span class="text-end"><code>{{ $tx->approval_code ?: '—' }}</code></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">invoice</span>
                            <span class="text-end"><code>{{ $tx->invoice ?: '—' }}</code></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Estado ePayco</span>
                            <span class="text-end">
                                {{ $tx->cod_estado ?: '—' }}
                                @if ($tx->respuesta)
                                    — {{ $tx->respuesta }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Motivo</span>
                            <span class="text-end">{{ $tx->motivo ?: '—' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush border rounded mb-0 h-100">
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Monto</span>
                            <span class="text-end">
                                @if ($tx->amount !== null)
                                    ${{ number_format((float) $tx->amount, 2, ',', '.') }}
                                    {{ $tx->currency }}
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Fecha ePayco</span>
                            <span class="text-end">{{ $tx->fecha_epayco ?: '—' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Banco</span>
                            <span class="text-end">{{ $tx->bank_name ?: '—' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Franquicia</span>
                            <span class="text-end">{{ $tx->franchise ?: '—' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Tarjeta</span>
                            <span class="text-end">{{ $tx->card_mask ?: '—' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-2">
                            <span class="text-muted">Cuotas</span>
                            <span class="text-end">{{ $tx->quotas ?: '—' }}</span>
                        </li>
                    </ul>
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
        <ul class="list-group list-group-flush border rounded mb-0">
            <li class="list-group-item py-2 text-muted">
                No hay transacciones ePayco registradas para esta precompra.
            </li>
        </ul>
    @endforelse
</div>
