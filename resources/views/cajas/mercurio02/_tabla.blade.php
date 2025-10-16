<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
        <th scope='col'>Caja</th>
        <th scope='col'>Nit</th>
        <th scope='col'>Razon Social</th>
        <th scope='col'>Acciones</th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            <tr>
                <td>{{ $mtable->getCodcaj() }}</td>
                <td>{{ $mtable->getNit() }}</td>
                <td>{{ $mtable->getRazsoc() }}</td>
                <td class='table-actions'>
                    <a href='#!' 
                        class='table-action btn btn-xs btn-primary' 
                        title='Editar' 
                        data-cid='{{ $mtable->getCodcaj() }}' 
                        data-toggle='editar'>
                            <i class='fas fa-user-edit text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>