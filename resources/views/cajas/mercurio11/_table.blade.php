<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th scope="col">Codigo</th>
            <th scope="col">Detalle</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody class="list">
        @foreach ($paginate->items as $mtable)
            <tr>
                <td>{{ $mtable->getCodest() }}</td>
                <td>{{ $mtable->getDetalle() }}</td>
                <td class="table-actions">
                    <a href="#!" class="table-action btn btn-primary btn-xs" title="Editar"
                        data-cid="{{ $mtable->getCodest() }}" data-toggle="editar">
                        <i class="fas fa-user-edit text-white"></i>
                    </a>
                    <a href="#!" class="table-action table-action-delete btn btn-danger btn-xs" title="Borrar"
                        data-cid="{{ $mtable->getCodest() }}" data-toggle="borrar">
                        <i class="fas fa-trash text-white"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
