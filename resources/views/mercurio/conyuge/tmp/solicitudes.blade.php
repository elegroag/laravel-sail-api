@foreach ($conyuges as $solicitud)
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
                        <button type="button" class='btn btn-info btn-sm' data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-eye text-white'></i> Corregir
                        </button>
                    </span>
                @elseif ($solicitud['estado'] == 'A')
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-success btn-sm' data-toggle='event-show' data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-hand-pointer-o text-white'></i> OK
                        </button>
                    </span>
                @elseif ($solicitud['estado'] == 'X')
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-sm bg-gray text-white' data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-hand-pointer-o text-white'></i> Rechazado
                        </button>
                    </span>
                @else
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-warning btn-sm' data-toggle='event-detalle' data-cid="{{ $solicitud['id'] }}">
                            <i class='fas fa-eye text-white'></i> Seguimiento
                        </button>
                    </span>
                @endif
                @if ($solicitud['estado'] != 'A')
                    <span style='margin-left:2px'>
                        <button type="button" class='btn btn-danger btn-sm' data-toggle='cancel-solicitud' data-cid="{{ $solicitud['id'] }}">Borrar</button>
                    </span>
                @endif
            </div>
        </td>
        <td>
            <p>{{ $solicitud['cedcon'] }}, {{ Tag::capitalize($solicitud['prinom'] . ' ' . $solicitud['segnom'] . ' ' . $solicitud['priape'] . ' ' . $solicitud['segape']) }}</p>
        </td>
        <td>
            <p>{{ $solicitud['estado_detalle'] }}</p>
        </td>
        <td>
            <p> {{ $solicitud['fecha_ultima_solicitud'] ?: "No se ha realizado ningún envío para validación" }}
                N° {{ $solicitud['cantidad_eventos'] }}</p>
        </td>

        <td>{{ $solicitud['fecsol'] }}</td>
    </tr>
@endforeach