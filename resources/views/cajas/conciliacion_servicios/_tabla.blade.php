@php
    use App\Services\Ecommerce\ConciliacionServiciosService;

    $badgeClass = [
        ConciliacionServiciosService::CONCILIADA => 'badge-conciliada',
        ConciliacionServiciosService::PAGADA_SIN_VENTA => 'badge-pagada-sin-venta',
        ConciliacionServiciosService::VALOR_DIFERENTE => 'badge-valor-diferente',
        ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA => 'badge-venta-sin-pa',
        ConciliacionServiciosService::ACEPTADA_EPAYCO_NO_PA => 'badge-aceptada-epayco-no-pa',
        ConciliacionServiciosService::SIN_TX_EPAYCO => 'badge-sin-tx-epayco',
        ConciliacionServiciosService::VENTA_NO_APROBADA => 'badge-venta-no-aprobada',
    ];
@endphp

<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered table-sm">
    <thead class="thead-light">
        <tr>
            <th>Estado</th>
            <th>Precompra</th>
            <th>Documento</th>
            <th>Servicio</th>
            <th>Ref ePayco</th>
            <th>Valor precompra</th>
            <th>Fecha pago</th>
            <th>Venta Subsidio</th>
            <th>Valor venta</th>
            <th>Fecha venta</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($paginate->items as $fila)
            @php
                $badge = $badgeClass[$fila['estado']] ?? 'bg-secondary';
                $ventaLabel = ($fila['venta_marca'] && $fila['venta_documento'])
                    ? $fila['venta_marca'] . '-' . $fila['venta_documento']
                    : '—';
            @endphp
            <tr data-estado="{{ $fila['estado'] }}" data-precompra-id="{{ $fila['precompra_id'] }}">
                <td>
                    <span class="badge {{ $badge }}">{{ $fila['estado_descripcion'] }}</span>
                    @if (! empty($fila['estado_precompra']) && in_array($fila['estado'], [
                        ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA,
                        ConciliacionServiciosService::ACEPTADA_EPAYCO_NO_PA,
                    ], true))
                        <div class="small text-muted">Precompra: {{ $fila['estado_precompra_desc'] ?? $fila['estado_precompra'] }}</div>
                    @endif
                    @if ($fila['estado'] === ConciliacionServiciosService::VENTA_NO_APROBADA && ! empty($fila['venta_estado']))
                        <div class="small text-muted">Venta: {{ $fila['venta_estado'] }}</div>
                    @endif
                </td>
                <td>
                    @if ($fila['precompra_id'])
                        <a href="{{ route('admservicios.ver', ['id' => $fila['precompra_id']]) }}" target="_blank" rel="noopener">
                            #{{ $fila['precompra_id'] }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $fila['documento'] ?: '—' }}</td>
                <td>
                    @if ($fila['codser'] !== '')
                        {{ $fila['codser'] }} / {{ $fila['numero'] }}
                    @else
                        —
                    @endif
                </td>
                <td><code class="small">{{ $fila['ref_payco'] }}</code></td>
                <td>{{ $fila['valor_precompra'] !== null ? number_format($fila['valor_precompra'], 2, ',', '.') : '—' }}</td>
                <td>{{ $fila['fecha_pago'] ?: '—' }}</td>
                <td>{{ $ventaLabel }}</td>
                <td>
                    @if ($fila['venta_valor_forma_pago'] !== null)
                        {{ number_format($fila['venta_valor_forma_pago'], 2, ',', '.') }}
                    @elseif ($fila['venta_valpago'] !== null)
                        {{ number_format($fila['venta_valpago'], 2, ',', '.') }}
                    @else
                        —
                    @endif
                </td>
                <td>{{ $fila['venta_fecha'] ?: '—' }}</td>
                <td class="text-center">
                    @if (! empty($fila['puede_registrar']))
                        <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                data-toggle="registrar-subsidio-conciliacion"
                                data-id="{{ $fila['precompra_id'] }}">
                            Registrar en Subsidio
                        </button>
                    @else
                        —
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center text-muted">Sin diferencias ni coincidencias en el rango indicado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
