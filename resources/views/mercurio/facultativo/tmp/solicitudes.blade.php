@if(count($facultativos) == 0)
    <caption>
        ¡No hay solicitudes disponibles para mostrar!
    </caption>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
@endif

@foreach ($facultativos as $solicitud)
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
                            <button type="button" class='btn btn-info btn-sm' data-toggle='event-proceso' data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-eye text-white'></i> Corregir
                            </button>
                        </span>
                        @break
                    @case('A')
                        <span class='ml-2'>
                            <button type="button" class='btn btn-success btn-sm' data-toggle='event-show' data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-hand-pointer'></i> OK
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
                        <button type="button" class='btn btn-danger btn-sm' data-toggle='cancel-solicitud' data-cid="{{ $solicitud['id'] }}">
                            <i class="fas fa-trash"></i>
                            Borrar
                        </button>
                    </span>
                @endif
            </div>
        </td>
        <td>
            <p class="text-sm  mb-0">
                Identificación {{ $solicitud['cedtra'] }} {{ capitalize($solicitud['razsoc']) }} {{ $solicitud['tipo_persona'] }}<br />De {{ $solicitud['detalle_zona'] }}
            </p>
        </td>
        <td>
            <p class="text-sm  mb-0">{{ $solicitud['estado_detalle'] }}</p>
        </td>
        <td>
            <p class="text-sm  mb-0">
                {{ ($solicitud['fecha_ultima_solicitud']) ? $solicitud['fecha_ultima_solicitud'] : "<br/>No se ha realizado ningún envío para validación" }}
                N° {{ $solicitud['cantidad_eventos'] }}
            </p>
        </td>
        <td>
            <p class="text-sm  mb-0">{{ $solicitud['fecest'] }}</p>
        </td>
    </tr>
@endforeach
