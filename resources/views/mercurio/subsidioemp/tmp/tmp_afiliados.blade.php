<table id="dataTable" class="table table-hover align-items-center table-bordered">
    <thead>
        <tr>
            <th scope="col">Options</th>
            <th scope="col">Cedula</th>
            <th scope="col">Nombre </th>
            <th scope="col">Salario</th>
            <th scope="col">Fecha Afiliacion</th>
            <th scope="col">Categoria UVT</th>
            <th scope="col">Estado</th>
            <th scope="col">Fecha Estado</th>
        </tr>
    </thead>
    <tbody class="list">
        @if (count($trabajadores) === 0)
            <tr align="center">
                <td colspan="8">No hay datos para mostrar</td>
            </tr>
        @else
            @foreach ($trabajadores as $msubsi15)
                <tr>
                    <td class="table-actions">
                        <a href="#!" class="btn btn-xs btn-primary text-white" data-event="ver_nucleo_familiar" data-cedtra="{{ $msubsi15['cedtra'] }}">
                            <i class="fas fa-folder-open"></i>
                        </a>
                    </td>
                    <td>{{ $msubsi15['cedtra'] }}</td>
                    <td>{{ $msubsi15['nombre'] }}</td>
                    <td>${{ number_format($msubsi15['salario'], 0, ',', '.') }}</td>
                    <td>{{ $msubsi15['fecafi'] }}</td>
                    <td>{{ $msubsi15['codcat'] }}</td>
                    <td>{{ $msubsi15['estado'] }}</td>
                    <td>{{ $msubsi15['fecest'] }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>