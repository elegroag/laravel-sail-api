<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col' style='width: 10%;'>Options</th>
            <th scope='col' style='width: 20%;'>Tipo Servicio</th>
            <th scope='col' style='width: 20%;'>Tipo Sociedad</th>
            <th scope='col' style='width: 20%;'>Documento</th>
            <th scope='col' style='width: 20%;'>Obligatorio</th>
        </tr>
    </thead>
    <tbody class='list'>
    @foreach ($paginate->items as $mtable)
        <tr>
            <td class='table-actions'>
                <a href='#!'
                    class='table-action btn btn-primary btn-xs'
                    title='Editar'
                    data-tipopc='{{$mtable->tipopc}}'
                    data-tipsoc='{{$mtable->tipsoc}}'
                    data-coddoc='{{$mtable->coddoc}}'
                    data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                </a>
                <a href='#!'
                    class='table-action table-action-delete btn btn-danger btn-xs'
                    title='Borrar'
                    data-tipopc='{{$mtable->tipopc}}'
                    data-tipsoc='{{$mtable->tipsoc}}'
                    data-coddoc='{{$mtable->coddoc}}'
                    data-toggle='borrar'>
                        <i class='fas fa-trash text-white'></i>
                </a>
            </td>
            <td>{{ $mtable->mercurio09->detalle }}</td>
            <td>{{ $mtable->subsi54->detalle }}</td>
            <td>{{ $mtable->mercurio12->detalle }}</td>
            <td>{{ $mtable->obliga == 1 || $mtable->obliga == 'S' ? 'SI' : 'NO' }}</td>

        </tr>
        @endforeach
        </tbody>
    </table>
