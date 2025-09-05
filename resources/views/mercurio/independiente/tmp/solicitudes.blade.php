@foreach ($empresas as $solicitud)
    <tr>
        <td>
            <div class="btn-group" role="group">
                @if ($solicitud['estado'] == 'T')
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-primary btn-sm' data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-user-edit text-white'></i> Editar
                        </button>
                    </span>
                @elseif ($solicitud['estado'] == 'D')
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-info btn-sm' data-toggle='event-proceso' data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-eye text-white'></i> Corregir
                        </button>
                    </span>
                @elseif ($solicitud['estado'] == 'A')
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-success btn-sm' data-toggle='event-show' data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-hand-pointer'></i> OK
                        </button>
                    </span>
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-sm btn-primary' data-toggle='event-cuenta' data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-cog text-white'></i> Administrar
                        </button>
                    </span>
                @else
                    @if ($solicitud['estado'] != 'X')
                        <span style='margin-left:2px'>
                            <button type="button" class='btn btn-warning btn-sm' data-toggle='event-detalle' data-cid="{{ $solicitud['id'] }}">
                                <i class='fas fa-eye text-white'></i> Seguimiento
                            </button>
                        </span>
                    @endif
                @endif
                @if ($solicitud['estado'] != 'A')
                    <span style='margin-left:2px'>
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