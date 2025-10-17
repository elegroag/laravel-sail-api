<table class='table table-hover table-bordered align-items-center'>
    <thead>
        <tr>
            <th>Id</th>
            <th>Documento</th>
            <th>Nombre</th>
            <th>Acción</th>
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
                        <i class='fas fa-info'></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>