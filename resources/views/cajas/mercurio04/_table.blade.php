
<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
        <th scope='col'>Codigo</th>
        <th scope='col'>Detalle</th>
        <th scope='col'>Principal</th>
        <th scope='col'>Estado</th>
        <th scope='col'>Options</th>
        </tr>
    </thead>
    <tbody class='list'>
    @foreach ($paginate->items as $mtable)
        <tr>
            <td>{{$mtable->getCodofi()}}</td>
            <td>{{$mtable->getDetalle()}}</td>
            <td>{{$mtable->getPrincipalDetalle()}}</td>
            <td>{{$mtable->getEstadoDetalle()}}</td>
            <td class='table-actions'>
                <a href='#!' class='btn btn-xs btn-primary' data-cid='{{ $mtable->getCodofi() }}' data-toggle='ciudad-view'>
                <i class='fas fa-city'></i>
                </a>&nbsp;
                <a href='#!' class='btn btn-xs btn-success' data-cid='{{ $mtable->getCodofi() }}' data-toggle='opcion-view'>
                <i class='fas fa-clipboard-list text-white'></i>
                </a>&nbsp;
                <a href='#!' class='btn btn-xs btn-warning' data-cid='{{ $mtable->getCodofi() }}' data-toggle='editar'>
                <i class='fas fa-user-edit text-white'></i>
                </a>&nbsp;
                <a href='#!' class='btn btn-xs btn-danger' data-cid='{{ $mtable->getCodofi() }}' data-toggle='borrar'>
                <i class='fas fa-trash text-white'></i>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
