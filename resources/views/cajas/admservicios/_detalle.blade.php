@php
    use App\Services\Ecommerce\EstadoDevolucionPrecompra;
    use App\Services\Ecommerce\EstadoPrecompra;

    $badgeEstado = [
        EstadoPrecompra::PENDIENTE => 'bg-warning text-dark',
        EstadoPrecompra::PAGADO => 'bg-success',
        EstadoPrecompra::DESESTIMADO => 'badge-estado-desestimado',
        EstadoPrecompra::RECHAZADO => 'bg-danger',
        EstadoPrecompra::ABANDONADA => 'bg-dark',
        EstadoPrecompra::VENCIDA => 'bg-warning text-dark',
        EstadoPrecompra::DEVOLUCION => 'bg-info text-dark',
    ];

    $badgeDevolucion = [
        EstadoDevolucionPrecompra::SOLICITADA => 'bg-warning text-dark',
        EstadoDevolucionPrecompra::EN_TRAMITE => 'bg-primary',
        EstadoDevolucionPrecompra::APROBADO => 'bg-success',
        EstadoDevolucionPrecompra::RECHAZADA => 'bg-danger',
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
    <div class="border rounded p-3">
        <div class="row g-3">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Documento titular</div>
                <div class="fw-semibold">{{ $precompra->documento }}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Beneficiario(s)</div>
                <div class="fw-semibold text-break">{{ $codbens !== [] ? implode(', ', $codbens) : '—' }}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Servicio / apertura</div>
                <div class="fw-semibold">{{ $precompra->codser }} — {{ $precompra->numero }}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Valor</div>
                <div class="fw-semibold">${{ number_format((float) $precompra->valor, 0, ',', '.') }}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Referencia ePayco</div>
                <div class="text-break"><code>{{ $precompra->ref_payco ?: '—' }}</code></div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Fecha precompra</div>
                <div>{{ optional($precompra->fecha_precompra)->format('Y-m-d H:i:s') ?: '—' }}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Fecha pago</div>
                <div>{{ optional($precompra->fecha_pago)->format('Y-m-d H:i:s') ?: '—' }}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="small text-muted">Código / motivo ePayco</div>
                <div>
                    {{ $precompra->cod_estado_epayco ?: '—' }}
                    @if ($precompra->motivo_epayco)
                        — {{ $precompra->motivo_epayco }}
                    @endif
                </div>
            </div>
            @if ($motivo !== '')
                <div class="col-12">
                    <div class="small text-muted">Motivo desestimación / abandono</div>
                    <div>{{ $motivo }}</div>
                    @if ($precompra->fecha_desestimacion)
                        <div class="text-muted small mt-1">
                            Fecha: {{ $precompra->fecha_desestimacion->format('Y-m-d H:i:s') }}
                        </div>
                    @endif
                </div>
            @endif
            @if ($precompra->motivo_vencida || $precompra->isVencida() || $precompra->isDevolucion())
                <div class="col-12">
                    <div class="small text-muted">Motivo vencimiento</div>
                    <div>{{ $precompra->motivo_vencida_descripcion ?: '—' }}</div>
                    @if ($precompra->detalle_vencida)
                        <div class="text-muted small mt-1">{{ $precompra->detalle_vencida }}</div>
                    @endif
                    @if ($precompra->fecha_vencida)
                        <div class="text-muted small mt-1">
                            Fecha: {{ $precompra->fecha_vencida->format('Y-m-d H:i:s') }}
                        </div>
                    @endif
                </div>
            @endif
            @if ($precompra->nota)
                <div class="col-12">
                    <div class="small text-muted">Nota</div>
                    <div>{{ $precompra->nota }}</div>
                </div>
            @endif
        </div>
    </div>

    @if (! empty($puedeMarcarVencida))
        <div class="border rounded p-3 mt-3">
            <h6 class="heading-small text-muted mb-2">Marcar como vencida</h6>
            <p class="small text-muted mb-2">
                Use esta acción si Subsidio rechazó el registro por cupos o plazo y la precompra sigue en Pagado.
            </p>
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small mb-1" for="motivo_vencida">Motivo</label>
                    <select id="motivo_vencida" class="form-select form-select-sm">
                        @foreach (($motivosVencida ?? []) as $codigo => $label)
                            <option value="{{ $codigo }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label small mb-1" for="detalle_vencida">Detalle (opcional)</label>
                    <input type="text" id="detalle_vencida" class="form-control form-control-sm" maxlength="1000">
                </div>
                <div class="col-12 col-md-3">
                    <button type="button" class="btn btn-sm btn-warning w-100" data-toggle="marcar-vencida" data-id="{{ $precompra->id }}">
                        Marcar vencida
                    </button>
                </div>
            </div>
        </div>
    @endif

    <h6 class="heading-small text-muted mb-3 mt-4">
        Solicitud de devolución
        @if ($precompra->devolucion)
            <span class="badge {{ $badgeDevolucion[$precompra->devolucion->estado] ?? 'bg-secondary' }} ms-1">
                {{ $precompra->devolucion->estado_descripcion }}
            </span>
        @else
            <span class="badge bg-light text-dark ms-1">Sin solicitud</span>
        @endif
    </h6>

    @if ($precompra->devolucion)
        @php $dev = $precompra->devolucion; @endphp
        <div class="border rounded p-3">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="small text-muted">Fecha solicitud</div>
                    <div>{{ optional($dev->fecha_solicitud)->format('Y-m-d H:i:s') ?: '—' }}</div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="small text-muted">En trámite</div>
                    <div>{{ optional($dev->fecha_en_tramite)->format('Y-m-d H:i:s') ?: '—' }}</div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="small text-muted">Resolución</div>
                    <div>{{ optional($dev->fecha_resolucion)->format('Y-m-d H:i:s') ?: '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="small text-muted">Motivo solicitud</div>
                    <div>{{ $dev->motivo_solicitud ?: '—' }}</div>
                </div>
                @if ($dev->nota_afiliado)
                    <div class="col-12">
                        <div class="small text-muted">Nota afiliado</div>
                        <div>{{ $dev->nota_afiliado }}</div>
                    </div>
                @endif
                @if ($dev->nota_gestion)
                    <div class="col-12">
                        <div class="small text-muted">Nota gestión</div>
                        <div style="white-space: pre-wrap">{{ $dev->nota_gestion }}</div>
                    </div>
                @endif
                @if ($dev->usuario_gestion)
                    <div class="col-12 col-md-4">
                        <div class="small text-muted">Usuario gestión</div>
                        <div>{{ $dev->usuario_gestion }}</div>
                    </div>
                @endif
            </div>

            @if ($dev->estaAbierta())
                <div class="mt-3">
                    <label class="form-label small mb-1" for="nota_gestion_devolucion">Nota de gestión (opcional)</label>
                    <textarea id="nota_gestion_devolucion" class="form-control form-control-sm mb-2" rows="2" maxlength="1000"></textarea>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($dev->isSolicitada())
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="devolucion-en-tramite" data-id="{{ $precompra->id }}">
                                Poner en trámite
                            </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-success" data-toggle="devolucion-aprobar" data-id="{{ $precompra->id }}">
                            Aprobar devolución
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="devolucion-rechazar" data-id="{{ $precompra->id }}">
                            Rechazar
                        </button>
                    </div>
                    <div class="form-text">Al aprobar, la precompra pasa a estado <strong>Devolución</strong> y la solicitud a <strong>Aprobado</strong>.</div>
                </div>
            @endif
        </div>
    @elseif ($precompra->isVencida())
        <ul class="list-group list-group-flush border rounded mb-0">
            <li class="list-group-item py-2 text-muted">
                Compra vencida. Pendiente de que el afiliado solicite formalmente la devolución desde Mercurio.
            </li>
        </ul>
    @else
        <ul class="list-group list-group-flush border rounded mb-0">
            <li class="list-group-item py-2 text-muted">
                No aplica solicitud de devolución en el estado actual.
            </li>
        </ul>
    @endif

    <h6 class="heading-small text-muted mb-3 mt-4">
        Otras precompras relacionadas
        <span class="badge bg-light text-dark ms-1">{{ $relacionadas->count() }}</span>
    </h6>
    <div class="small text-muted mb-2">
        Mismo titular, servicio / apertura y día, con al menos un beneficiario en común.
    </div>

    @if ($relacionadas->isNotEmpty())
        <div class="table-responsive border rounded">
            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Hora</th>
                        <th scope="col">Beneficiario(s)</th>
                        <th scope="col" class="text-end">Valor</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Referencia ePayco</th>
                        <th scope="col">Fecha pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relacionadas as $otra)
                        <tr>
                            <td>
                                <a href="{{ route('admservicios.ver', ['id' => $otra->id]) }}" target="_blank" rel="noopener">
                                    {{ $otra->id }}
                                </a>
                            </td>
                            <td class="text-nowrap">{{ optional($otra->fecha_precompra)->format('H:i:s') }}</td>
                            <td class="text-break">{{ implode(', ', $otra->codbenList()) ?: '—' }}</td>
                            <td class="text-end text-nowrap">${{ number_format((float) $otra->valor, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $badgeEstado[$otra->estado] ?? 'bg-light text-dark' }}">
                                    {{ $otra->estado_descripcion }}
                                </span>
                            </td>
                            <td class="text-break"><code>{{ $otra->ref_payco ?: '—' }}</code></td>
                            <td class="text-nowrap">{{ optional($otra->fecha_pago)->format('Y-m-d H:i') ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <ul class="list-group list-group-flush border rounded mb-0">
            <li class="list-group-item py-2 text-muted">
                No hay otras precompras relacionadas.
            </li>
        </ul>
    @endif

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
        <div class="border rounded p-3">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Marca / documento</div>
                    <div class="fw-semibold">{{ ($venta['marca'] ?? '—') }}-{{ ($venta['documento'] ?? '—') }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Fecha / hora</div>
                    <div>{{ ($venta['fecha'] ?? '—') }} {{ $venta['hora'] ?? '' }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Estado</div>
                    <div>{{ $venta['estado_texto'] ?? ($venta['estado'] ?? '—') }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Ref. pago</div>
                    <div><code>{{ $venta['refpago'] ?? '—' }}</code></div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Titular</div>
                    <div class="fw-semibold">{{ $venta['cedtra_titular'] ?? '—' }} — {{ $venta['nombre_titular'] ?? '' }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Valor pago</div>
                    <div class="fw-semibold">${{ number_format((float) ($venta['valpago'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Valor subsidio</div>
                    <div>${{ number_format((float) ($venta['valsub'] ?? 0), 0, ',', '.') }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Forma de pago</div>
                    <div>{{ $venta['forma_pago_detalle'] ?? '—' }} ({{ $venta['codfor'] ?? '—' }})</div>
                </div>
                @if (! empty($venta['nota']))
                    <div class="col-12">
                        <div class="small text-muted">Nota</div>
                        <div>{{ $venta['nota'] }}</div>
                    </div>
                @endif
            </div>
        </div>

        @if (! empty($venta['items']) && is_array($venta['items']))
            <h6 class="heading-small text-muted mb-3 mt-3">Ítems de la venta</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm table-striped align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Sec</th>
                            <th scope="col">Beneficiario</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Servicio</th>
                            <th scope="col" class="text-end">Valor</th>
                            <th scope="col" class="text-end">Subsidio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta['items'] as $item)
                            <tr>
                                <td>{{ $item['sec'] ?? '—' }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $item['codben'] ?? '—' }}</span>
                                    — {{ $item['nombre_beneficiario'] ?? '' }}
                                </td>
                                <td>{{ $item['tipben_texto'] ?? ($item['tipben'] ?? '—') }}</td>
                                <td>{{ $item['codser'] ?? '' }} / {{ $item['numero'] ?? '' }} {{ $item['nombre_servicio'] ?? '' }}</td>
                                <td class="text-end text-nowrap">${{ number_format((float) ($item['valser'] ?? 0), 0, ',', '.') }}</td>
                                <td class="text-end text-nowrap">${{ number_format((float) ($item['valsub'] ?? 0), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
        <div class="border rounded p-3 mb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div>
                    <span class="badge bg-info text-dark">{{ $origenLabel[$tx->origen] ?? $tx->origen }}</span>
                    <span class="text-muted small ms-1">#{{ $tx->id }}</span>
                </div>
                <div class="small text-muted">
                    {{ optional($tx->created_at)->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">ref_payco</div>
                    <div class="text-break"><code>{{ $tx->ref_payco ?: '—' }}</code></div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">transaction_id</div>
                    <div class="text-break"><code>{{ $tx->transaction_id ?: '—' }}</code></div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">approval_code</div>
                    <div class="text-break"><code>{{ $tx->approval_code ?: '—' }}</code></div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">invoice</div>
                    <div class="text-break"><code>{{ $tx->invoice ?: '—' }}</code></div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Estado ePayco</div>
                    <div>
                        {{ $tx->cod_estado ?: '—' }}
                        @if ($tx->respuesta)
                            — {{ $tx->respuesta }}
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Motivo</div>
                    <div>{{ $tx->motivo ?: '—' }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Monto</div>
                    <div class="fw-semibold">
                        @if ($tx->amount !== null)
                            ${{ number_format((float) $tx->amount, 2, ',', '.') }}
                            {{ $tx->currency }}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Fecha ePayco</div>
                    <div>{{ $tx->fecha_epayco ?: '—' }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Banco</div>
                    <div>{{ $tx->bank_name ?: '—' }}</div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="small text-muted">Franquicia</div>
                    <div>{{ $tx->franchise ?: '—' }}</div>
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
