@if(count($conyuges) == 0)
    <caption>
        <p>¡No hay solicitudes disponibles para mostrar!</p>
    </caption>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
@endif

@foreach ($conyuges as $solicitud)
    <tr>
        <td>
            <div class="btn-group" role="group">
                @switch($solicitud['estado'])
                    @case('T')
                        <span class='ml-2'>
                            <button type="button" class='btn btn-primary btn-sm' data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-user-edit text-white'></i> Editar
                            </button>
                        </span>
                        @break
                    @case('D')
                        <span class='ml-2'>
                            <button type="button" class='btn btn-info btn-sm' data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-eye text-white'></i> Corregir
                            </button>
                        </span>
                        @break
                    @case('A')
                        <span class='ml-2'>
                            <button type="button" class='btn btn-success btn-sm' data-toggle='event-show' data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-hand-pointer-o text-white'></i> OK
                            </button>
                        </span>
                        @break
                    @case('P')
                        <span class='ml-2'>
                            <button type="button" class='btn btn-warning btn-sm' data-toggle='event-detalle' data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-eye text-white'></i> Seguimiento
                            </button>
                        </span>
                        @break
                    @default
                        <span class='ml-2'>
                            <button type="button" class="btn btn-default btn-sm ml-1" disabled>
                                <i class="fas fa-times text-white"></i> Sin acción
                            </button>
                        </span>
                @endswitch
                @if ($solicitud['estado'] != 'A')
                    <span class='ml-2'>
                        <button type="button" class='btn btn-danger btn-sm' data-toggle='cancel-solicitud' data-cid="{{ $solicitud['id'] }}">Borrar</button>
                    </span>
                @endif
            </div>
        </td>
        <td>
            {{ $solicitud['ruuid'] }}
        </td>
        <td>
            {{ $solicitud['cedcon'] }}, {{ capitalize($solicitud['prinom'] . ' ' . $solicitud['segnom'] . ' ' . $solicitud['priape'] . ' ' . $solicitud['segape']) }}
        </td>
        <td>
            {{ $solicitud['estado_detalle'] }}
        </td>
        <td>
            {{ $solicitud['fecha_ultima_solicitud'] ?: "No se ha realizado ningún envío para validación" }}
            N° {{ $solicitud['cantidad_eventos'] }}
        </td>
        <td>{{ $solicitud['fecsol'] }}</td>
        <td>{{ $solicitud['id'] }}</td>
    </tr>
@endforeach