<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Codigo</th>
            <th scope='col'>Email</th>
            <th scope='col'>Telefono</th>
            <th scope='col'>Nota</th>
            <th scope='col'>Estado</th>
            <th scope='col'>Archivo</th>
            <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            <tr>
                <td>{{ $mtable->codinf }}</td>
                <td>{{ $mtable->email }}</td>
                <td>{{ $mtable->telefono }}</td>
                <td>{{ $mtable->nota }}</td>
                <td>{{ $mtable->estado == 'A' ? 'Activo' : 'Inactivo' }}</td>
                <td>{{ $mtable->archivo }}</td>
                <td class='table-actions'>
                    <a href='/mercurio57/index/{{ $mtable->codinf }}'
                        class='table-action btn btn-xs btn-primary'
                        title='Servicios'>
                        <i class='fas fa-clipboard-list text-white'></i>
                    </a>
                    <a href='#!'
                        class='table-action btn btn-xs btn-primary'
                        title='Editar'
                        data-cid='{{ $mtable->codinf }}'
                        data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!'
                        class='table-action table-action-delete btn btn-xs btn-danger'
                        title='Borrar'
                        data-cid='{{ $mtable->codinf }}'
                        data-toggle='borrar'>
                        <i class='fas fa-trash text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
