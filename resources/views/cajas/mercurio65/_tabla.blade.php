<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Nit</th>
            <th scope='col'>Razon Social</th>
            <th scope='col'>Direccion</th>
            <th scope='col'>Email</th>
            <th scope='col'>Clasificacion</th>
            <th scope='col'>Estado</th>
            <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            <tr>
                <td>{{ $mtable->getNit() }}</td>
                <td>{{ $mtable->getRazsoc() }}</td>
                <td>{{ $mtable->getDireccion() }}</td>
                <td>{{ $mtable->getEmail() }}</td>
                <td>{{ $mtable->getCodclaDetalle() }}</td>
                <td>{{ $mtable->getEstadoDetalle() }}</td>
                <td class='table-actions'>
                    <a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{$mtable->getCodsed()}\")'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{$mtable->getCodsed()}\")'>
                        <i class='fas fa-trash text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
