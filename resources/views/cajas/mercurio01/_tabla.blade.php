<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Aplicativo</th>
            <th scope='col'>Email</th>
            <th scope='col'>Path</th>
            <th scope='col'>Server</th>
            <th scope='col'>Option</th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            <tr>
                <td>{{ $mtable->getCodapl() }}</td>
                <td>{{ $mtable->getEmail() }}</td>
                <td>{{ $mtable->getPath() }}</td>
                <td>{{ $mtable->getFtpserver() }}</td>
                <td class='table-actions'>
                    <a href='#!' 
                        class='table-action btn btn-xs btn-primary' 
                        title='editar' 
                        data-cid='{{ $mtable->getCodapl() }}' 
                        data-toggle='editar'>
                            <i class='fas fa-user-edit text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>