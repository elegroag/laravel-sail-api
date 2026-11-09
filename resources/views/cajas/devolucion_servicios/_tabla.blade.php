@php
    use App\Services\Ecommerce\EstadoDevolucionPrecompra;

    $badgeDev = [
        EstadoDevolucionPrecompra::SOLICITADA => 'badge-dev-solicitada',
        EstadoDevolucionPrecompra::EN_TRAMITE => 'badge-dev-tramite',
        EstadoDevolucionPrecompra::APROBADO => 'badge-dev-aprobado',
        EstadoDevolucionPrecompra::RECHAZADA => 'badge-dev-rechazada',
    ];
@endphp

<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered table-sm align-middle">
    <thead class="thead-light">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Precompra</th>
            <th scope="col">Documento</th>
            <th scope="col">Servicio</th>
            <th scope="col" class="text-end">Valor</th>
            <th scope="col">Estado precompra</th>
            <th scope="col">Estado devolución</th>
            <th scope="col">Fecha solicitud</th>
            <th scope="col">Motivo / nota afiliado</th>
            <th scope="col" class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($paginate->items as $dev)
            @php
                $pre = $dev->precompra;
                $abierta = $dev->estaAbierta();
            @endphp
            <tr>
                <td>{{ $dev->id }}</td>
                <td>
                    @if ($pre)
                        <a href="{{ route('admservicios.ver', ['id' => $pre->id]) }}" target="_blank" rel="noopener">
                            #{{ $pre->id }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $pre->documento ?? '—' }}</td>
                <td>
                    @if ($pre)
                        {{ $pre->codser }} — {{ $pre->numero }}
                    @else
                        —
                    @endif
                </td>
                <td class="text-end text-nowrap">
                    ${{ number_format((float) ($pre->valor ?? 0), 0, ',', '.') }}
                </td>
                <td>{{ $pre->estado_descripcion ?? ($pre->estado ?? '—') }}</td>
                <td>
                    <span class="badge {{ $badgeDev[$dev->estado] ?? 'bg-secondary' }}">
                        {{ $dev->estado_descripcion }}
                    </span>
                </td>
                <td class="text-nowrap">{{ optional($dev->fecha_solicitud)->format('Y-m-d H:i') ?: '—' }}</td>
                <td class="small">
                    <div>{{ $dev->motivo_solicitud ?: '—' }}</div>
                    @if ($dev->nota_afiliado)
                        <div class="text-muted mt-1">{{ \Illuminate\Support\Str::limit($dev->nota_afiliado, 120) }}</div>
                    @endif
                </td>
                <td class="text-center text-nowrap">
                    @if ($pre)
                        <a
                            href="{{ route('admservicios.ver', ['id' => $pre->id]) }}"
                            target="_blank"
                            rel="noopener"
                            class="btn btn-sm btn-outline-primary"
                            title="Ver detalle precompra"
                        >
                            <i class="fas fa-eye"></i>
                        </a>
                    @endif
                    @if ($abierta)
                        @if ($dev->isSolicitada())
                            <button
                                type="button"
                                class="btn btn-sm btn-primary"
                                data-toggle="dev-accion"
                                data-accion="en-tramite"
                                data-id="{{ $dev->id }}"
                                data-resumen="Solicitud #{{ $dev->id }} — precompra #{{ $pre->id ?? '' }}"
                                title="Poner en trámite"
                            >
                                <i class="fas fa-play"></i>
                            </button>
                        @endif
                        <button
                            type="button"
                            class="btn btn-sm btn-success"
                            data-toggle="dev-accion"
                            data-accion="aprobar"
                            data-id="{{ $dev->id }}"
                            data-resumen="Aprobar devolución #{{ $dev->id }} — precompra #{{ $pre->id ?? '' }}"
                            title="Aprobar"
                        >
                            <i class="fas fa-check"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                            data-toggle="dev-accion"
                            data-accion="rechazar"
                            data-id="{{ $dev->id }}"
                            data-resumen="Rechazar devolución #{{ $dev->id }} — precompra #{{ $pre->id ?? '' }}"
                            title="Rechazar"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    No hay solicitudes de devolución con los filtros aplicados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
