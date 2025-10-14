<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Codigo Aplicativo</th>
            <th scope='col'>Url Webservice</th>
            <th scope='col'>Path</th>
            <th scope='col'>Url Online</th>
            <th scope='col'>Puntos por Compartir</th>
            <th scope='col'>Acciones</th>
        </tr>
    </thead>
    <tbody class='list'>
    @foreach ($paginate->items as $mtable)
    <tr>
        <td>{{$mtable->getCodapl()}}</td>
        <td>{{$mtable->getWebser()}}</td>
        <td>{{$mtable->getPath()}}</td>
        <td>{{$mtable->getUrlonl()}}</td>
        <td>{{$mtable->getPuncom()}}</td>
        <td class='table-actions'>
            <a href='#!' class='table-action btn btn-xs btn-primary' title='Editar' data-cid='{{ $mtable->getCodapl() }}' data-toggle='editar'>
                <i class='fas fa-user-edit text-white'></i>
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>