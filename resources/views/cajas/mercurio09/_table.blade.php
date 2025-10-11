<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Tipopc</th>
            <th scope='col'>Detalle</th>
            <th scope='col'>Dias</th>
            <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
        <tr>
            <td>{{ $mtable->getTipopc() }}</td>
            <td>{{ $mtable->getDetalle() }}</td>
            <td>{{ $mtable->getDias() }}</td>
            <td class='table-actions'>
                <a type='button'
                    class='table-action btn btn-xs btn-warning'
                    title='Documento' data-cid='{{ $mtable->getTipopc() }}'
                    data-toggle='archivos-view'>
                    <i class='fas fa-file-image text-white'></i>
                </a>
                @if (! in_array($mtable->getTipopc(), ['1', '3', '4', '7']))
                <a type='button'
                    class='table-action btn btn-xs btn-success'
                    title='Documento'
                    data-cid='{{ $mtable->getTipopc() }}'
                    data-toggle='empresa-view'>
                    <i class='fas fa-eye text-white'></i>
                </a>
                @endif
                <a href='#!'
                    class='table-action btn btn-xs btn-primary'
                    title='Editar'
                    data-cid='{{ $mtable->getTipopc() }}'
                    data-toggle='editar'>
                    <i class='fas fa-user-edit text-white'></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
