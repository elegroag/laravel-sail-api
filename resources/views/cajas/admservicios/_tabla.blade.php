@php
    use App\Services\Ecommerce\EstadoPrecompra;

    $badgeEstado = [
        EstadoPrecompra::PENDIENTE => 'bg-warning text-dark',
        EstadoPrecompra::PAGADO => 'bg-success',
        EstadoPrecompra::DESESTIMADO => 'badge-estado-desestimado',
        EstadoPrecompra::RECHAZADO => 'bg-danger',
        EstadoPrecompra::ABANDONADA => 'bg-dark',
    ];
@endphp

<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>#</th>
            <th scope='col'>Documento</th>
            <th scope='col'>Beneficiario</th>
            <th scope='col'>Servicio</th>
            <th scope='col'>Valor</th>
            <th scope='col'>Estado</th>
            <th scope='col'>Referencia ePayco</th>
            <th scope='col'>Transaction ID</th>
            <th scope='col'>Approval code</th>
            <th scope='col'>Fecha precompra</th>
            <th scope='col'>Fecha pago</th>
            <th scope='col'>Motivo desestimación</th>
            <th scope='col' class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody class='list'>
        @forelse ($paginate->items as $precompra)
            @php
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
                        $motivo .= ' - ' . $precompra->detalle_desestimacion;
                    }
                }
                $tx = $precompra->ultimaTransaccionEpayco;
            @endphp
            <tr>
                <td>{{ $precompra->id }}</td>
                <td>{{ $precompra->documento }}</td>
                <td>{{ $precompra->codben }}</td>
                <td>{{ $precompra->codser }} - {{ $precompra->numero }}</td>
                <td>${{ number_format((float) $precompra->valor, 0, ',', '.') }}</td>
                <td>
                    <span class="badge {{ $badgeEstado[$precompra->estado] ?? 'bg-light text-dark' }}">
                        {{ $precompra->estado_descripcion }}
                    </span>
                </td>
                <td>{{ $precompra->ref_payco ?: '—' }}</td>
                <td class="text-nowrap"><code class="small">{{ $tx?->transaction_id ?: '—' }}</code></td>
                <td class="text-nowrap"><code class="small">{{ $tx?->approval_code ?: '—' }}</code></td>
                <td>{{ optional($precompra->fecha_precompra)->format('Y-m-d H:i') }}</td>
                <td>{{ optional($precompra->fecha_pago)->format('Y-m-d H:i') }}</td>
                <td>{{ $motivo }}</td>
                <td class="text-center text-nowrap">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-toggle="detalle-precompra"
                        data-id="{{ $precompra->id }}"
                        title="Ver detalle y transacciones"
                    >
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="text-center text-muted py-4">No se encontraron registros con los filtros aplicados.</td>
            </tr>
        @endforelse
    </tbody>
</table>
