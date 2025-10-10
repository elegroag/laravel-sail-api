<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col' style="width: 8%"></th>
            <th scope='col' style="width: 8%">Documento</th>
            <th scope='col' style="width: 80%">Detalle</th>
        </tr>
    </thead>
    <tbody class='list'>
    @foreach ($paginate->items as $mtable)
        <tr>
            <td class='table-actions'>
                <a href='#!' 
                    class='table-action btn btn-xs btn-primary' 
                    data-toggle='editar' 
                    data-cid='{{ $mtable->getCoddoc() }}' 
                    title='Editar'>
                    <i class='fas fa-user-edit text-white'></i>
                </a>
                <a href='#!' 
                    class='table-action table-action-delete btn btn-xs btn-danger' 
                    data-toggle='borrar' 
                    data-cid='{{ $mtable->getCoddoc() }}' 
                    title='Borrar'>
                    <i class='fas fa-trash text-white'></i>
                </a>
            </td>
            <td>{{$mtable->getCoddoc()}}</td>
            <td>{{$mtable->getDetalle()}}</td>
        </tr>
    @endforeach
    </tbody>
</table>