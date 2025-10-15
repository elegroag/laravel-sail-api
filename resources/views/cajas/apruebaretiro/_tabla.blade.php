<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
    <thead class='thead-light'>
        <tr>
            <th scope='col'>Cedula</th>
            <th scope='col'>Nombre</th>
            <th scope='col'>Dias</th>
            <th scope='col'></th>
        </tr>
    </thead>
    <tbody class='list'>";
        @foreach ($paginate->items as $mtable)
            @php
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mtable->getId(), $mtable->getFecret());
            @endphp
            @if ($dias_vencidos == 3)
            <tr style='background: #f1f1ad'>
            @elseif ($dias_vencidos > 3)
            <tr style='background: #f5b2b2'>
            @else
            <tr>
            @endif
                <td>{{$mtable->getCedtra()}}</td>
                <td>{{$mtable->getNomtra()}}</td>
                <td>{{$dias_vencidos}}</td>
                <td class='table-actions'>
                    <a href='#!' 
                        class='table-action btn btn-xs btn-primary' 
                        title='Info' 
                        data-cid='{{$mtable->getId()}}'
                        data-toogle="info">
                        <i class='fas fa-folder-open text-white'></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

