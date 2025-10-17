<table class='table table-hover table-bordered align-items-center'>
    <thead>
        <tr>
            <th width="10%">Id</th>
            <th width="20%">Documento</th>
            <th width="60%">Nombre</th>
            <th width="10%">Acción</th>
        </tr>
    </thead>
    <tbody class='list'>
        @if ($solicitudes->count() == 0)
            <tr>
                <td colspan="4">No hay solicitudes</td>
            </tr>
        @endif
        @foreach ($solicitudes as $solicitud)
        <tr>
            <td>{{$solicitud['id']}}</td>
            <td>{{$solicitud['documento']}}</td>
            <td>{{$solicitud['nombre']}}</td>
            <td>
                <a href='#!' 
                    class='table-action btn btn-xs btn-primary' 
                    title='Info' 
                    data-tipopc="{{ $tipopc }}"
                    data-id="{{ $solicitud['id'] }}"
                    data-toggle="info">
                        <i class='fas fa-hand-pointer text-white'></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>