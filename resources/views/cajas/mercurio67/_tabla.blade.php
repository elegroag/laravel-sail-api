<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Codigo</th>
            <th scope='col'>Detalle</th>
            <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            <tr>
                <td>{{ $mtable->getCodcla() }}</td>
                <td>{{ $mtable->getDetalle() }}</td>
                <td class='table-actions'>
                    <a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' onclick='editar(\"{{ $mtable->getCodcla() }}\")'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!' class='table-action table-action-delete btn btn-xs btn-danger' title='Borrar' onclick='borrar(\"{{ $mtable->getCodcla() }}\")'>
                        <i class='fas fa-trash text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
