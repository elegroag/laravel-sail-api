<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Codigo</th>
            <th scope='col'>Nota</th>
            <th scope='col'>Email</th>
            <th scope='col'>Pregunta cantidad</th>
            <th scope='col'>Automatico Servicio</th>
            <th scope='col'>Consumo</th>
            <th scope='col'>Estado</th>
            <th scope='col'>Imagen</th>
            <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>
        @foreach ($paginate->items as $mtable)
            @php
                $precanDetalle = $mtable->precan == 'S' ? 'Si' : 'No';
                $autserDetalle = $mtable->autser == 'S' ? 'Si' : 'No';
                $estadoDetalle = $mtable->estado == 'A' ? 'Activo' : 'Inactivo';
            @endphp
            <tr>
                <td>{{ $mtable->codser }}</td>
                <td>{{ $mtable->nota }}</td>
                <td>{{ $mtable->email }}</td>
                <td>{{ $precanDetalle }}</td>
                <td>{{ $autserDetalle }}</td>
                <td>{{ $mtable->consumo }}</td>
                <td>{{ $estadoDetalle }}</td>
                <td>{{ $mtable->archivo }}</td>
                <td class='table-actions'>
                    <a href='#!'
                        class='table-action btn btn-xs btn-primary'
                        title='Editar'
                        data-codinf='{{ $mtable->codinf }}'
                        data-codser='{{ $mtable->codser }}'
                        data-numero='{{ $mtable->numero }}'
                        data-toggle='editar'>
                        <i class='fas fa-user-edit text-white'></i>
                    </a>
                    <a href='#!'
                        class='table-action table-action-delete btn btn-xs btn-danger'
                        title='Borrar'
                        data-codinf='{{ $mtable->codinf }}'
                        data-codser='{{ $mtable->codser }}'
                        data-numero='{{ $mtable->numero }}'
                        data-toggle='borrar'>
                            <i class='fas fa-trash text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
