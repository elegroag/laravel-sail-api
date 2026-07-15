@php
    use App\Services\Ecommerce\EstadoPrecompra;

    $badgeEstado = [
        EstadoPrecompra::PENDIENTE => 'bg-warning text-dark',
        EstadoPrecompra::PAGADO => 'bg-success',
        EstadoPrecompra::DESESTIMADO => 'bg-secondary',
        EstadoPrecompra::RECHAZADO => 'bg-danger',
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
            <th scope='col'>Fecha precompra</th>
            <th scope='col'>Fecha pago</th>
            <th scope='col'>Motivo desestimación</th>
        </tr>
    </thead>
    <tbody class='list'>
        @forelse ($paginate->items as $precompra)
            @php
                $motivo = '';
                if ($precompra->motivo_desestimacion != '') {
                    $motivo = EstadoPrecompra::MOTIVOS_DESESTIMACION[$precompra->motivo_desestimacion] ?? $precompra->motivo_desestimacion;
                    if ($precompra->detalle_desestimacion != '') {
                        $motivo .= ' - ' . $precompra->detalle_desestimacion;
                    }
                }
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
                <td>{{ $precompra->ref_payco }}</td>
                <td>{{ optional($precompra->fecha_precompra)->format('Y-m-d H:i') }}</td>
                <td>{{ optional($precompra->fecha_pago)->format('Y-m-d H:i') }}</td>
                <td>{{ $motivo }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">No se encontraron registros con los filtros aplicados.</td>
            </tr>
        @endforelse
    </tbody>
</table>
