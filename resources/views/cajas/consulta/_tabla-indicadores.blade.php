<div class='table-responsive'>
    <table class='table table-bordered table-hover table-sm'>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Acc\Estado</th>
                <th>Aprobado</th>
                <th>Rechazado</th>
                <th>Pendiente</th>
                <th>Devuelto</th>
                <th class='text-center'>Total</th>
                <th class='text-center'>Vencidos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_indicadores as $mgener02)
                <tr>
                    <th class='align-middle' rowspan='{{ $mgener02['cantidad'] }}'>{{ $mgener02['nombre'] }}</th>
                    <td>{{ $mgener02['usuario'] }}</td>
                    <td>{{ $mgener02['detalle'] }}</td>
                    <td align='center'>{{ $mgener02['estado_aprobado'] }}</td>
                    <td align='center'>{{ $mgener02['estado_rechazo'] }}</td>
                    <td align='center'>{{ $mgener02['estado_pendiente'] }}</td>
                    <td align='center'>{{ $mgener02['estado_devuelto'] }}</td>
                    <td align='center'></td>
                    <td align='center'>{{ $mgener02['total_vencido'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>