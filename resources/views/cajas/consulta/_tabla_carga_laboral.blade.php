<div class="table-responsive">
    <table id="tablaSolicitudesCarga" class="table table-sm table-hover table-bordered align-middle w-100 carga-solicitudes-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Fecha sol.</th>
                <th>Días</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitudes as $solicitud)
                <tr>
                    <td>{{ $solicitud['id'] }}</td>
                    <td>{{ $solicitud['documento'] }}</td>
                    <td>{{ $solicitud['nombre'] }}</td>
                    <td>{{ $solicitud['fecsol'] }}</td>
                    <td>{{ $solicitud['dias'] }}</td>
                    <td>
                        <span class="badge badge-pill badge-warning text-dark">{{ $solicitud['estado'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
