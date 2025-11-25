@if(count($empresas) == 0)
    <caption>
        ¡No hay solicitudes disponibles para mostrar!
    </caption>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
@endif

@foreach ($empresas as $solicitud)
    <tr>
        <td>
            <div class="btn-group" role="group">
                {{-- Botones según estado --}}
                @switch($solicitud['estado'])
                    @case('T')
                        <button type="button" class="btn btn-primary btn-sm ml-1" data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                            <i class="fas fa-user-edit text-white"></i> Editar
                        </button>
                        @break
                    
                    @case('D')
                        <button type="button" class="btn btn-info btn-sm ml-1" data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                            <i class="fas fa-eye text-white"></i> Corregir
                        </button>
                        @break
                    
                    @case('A')
                        <button type="button" class="btn btn-success btn-sm ml-1" data-toggle="event-show" data-cid="{{ $solicitud['id'] }}">
                            <i class="fas fa-hand-pointer"></i> OK
                        </button>
                        <button type="button" class="btn btn-sm btn-primary ml-1" data-toggle="event-cuenta" data-cid="{{ $solicitud['id'] }}">
                            <i class="fas fa-cog text-white"></i> Administrar
                        </button>
                        @break
                    
                    @default
                        @if($solicitud['estado'] != 'X')
                            <button type="button" class="btn btn-warning btn-sm ml-1" data-toggle="event-detalle" data-cid="{{ $solicitud['id'] }}">
                                <i class="fas fa-eye text-white"></i> Seguimiento
                            </button>
                        @endif
                @endswitch
                
                {{-- Botón Borrar --}}
                @if($solicitud['estado'] != 'A')
                    <button type="button" class="btn btn-danger btn-sm ml-1" data-toggle="cancel-solicitud" data-cid="{{ $solicitud['id'] }}">
                        <i class="fas fa-trash"></i> Borrar
                    </button>
                @endif
            </div>
        </td>
        <td>
            {{ $solicitud['ruuid'] }}
        </td>
        <td>
            Identificación {{ $solicitud['nit'] }}
            {{ ucfirst($solicitud['razsoc']) }}
            Empresa {{ $solicitud['tipo_persona'] }} De {{ $solicitud['detalle_zona'] }}
        </td>
        <td>
            {{ $solicitud['estado_detalle'] }}
        </td>
        <td>
            {{ $solicitud['fecha_ultima_solicitud'] ?: "No se ha realizado ningún envío para validación" }}
            N° {{ $solicitud['cantidad_eventos'] }}
        </td>
        <td>
            {{ $solicitud['fecest'] ?: $solicitud['fecini'] }}
        </td>
    </tr>
@endforeach
