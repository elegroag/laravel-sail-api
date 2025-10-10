<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col' style="width: 10%;">Options</th>
            <th scope='col' style="width: 10%;">Tipo</th>
            <th scope='col' style="width: 80%;">Detalle</th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            <tr>
                <td class='table-actions'>
                    <a href='#!' class='table-action btn btn-xs btn-warning' 
                        title='Campos' 
                        data-toggle='campo_view'
                        data-cid='{{ $mtable->tipo }}'>
                        <i class='fas fa-shield-alt text-white'></i>
                    </a>
                    <a href='#!' 
                        class='table-action btn btn-xs btn-primary' 
                        title='Editar' 
                        data-cid='{{ $mtable->tipo }}' 
                        data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!' 
                        class='table-action table-action-delete btn btn-xs btn-danger' 
                        title='Borrar' 
                        data-cid='{{ $mtable->tipo }}' 
                        data-toggle='borrar'>
                        <i class='fas fa-trash text-white'></i>
                    </a>
                </td>
                <td>{{ $mtable->tipo }}</td>
                <td>{{ $mtable->detalle }}</td>
            </tr>
        @endforeach
    </tbody>
</table>