<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
        <th scope='col'>Codigo</th>
        <th scope='col'>Detalle</th>
        <th scope='col'>Categoria Padre</th>
        <th scope='col'>Tipo</th>
        <th scope='col'>Estado</th>
        <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>
    @foreach ($paginate->items as $mtable)
    @php
        $tipoDetalle = $mtable->tipo == 'P' ? 'Producto' : ($mtable->tipo == 'S' ? 'Servicio' : 'N/A');
        $estadoDetalle = $mtable->estado == 'A' ? 'Activo' : 'Inactivo';
        $parentDetalle = $mtable->parent->detalle ?? 'Principal';
    @endphp
        <tr>
            <td>{{$mtable->codcat}}</td>
            <td>{{$mtable->detalle}}</td>
            <td>{{$parentDetalle}}</td>
            <td>{{$tipoDetalle}}</td>
            <td>{{$estadoDetalle}}</td>
            <td class='table-actions'>
                <a href='#!' 
                    class='table-action btn btn-xs btn-primary' 
                    title='Editar' 
                    data-cid='{{$mtable->codcat}}' 
                    data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                </a>
                <a href='#!' 
                    class='table-action table-action-delete btn btn-xs btn-danger' 
                    title='Borrar' 
                    data-cid='{{$mtable->codcat}}' 
                    data-toggle='borrar'>
                        <i class='fas fa-trash text-white'></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>