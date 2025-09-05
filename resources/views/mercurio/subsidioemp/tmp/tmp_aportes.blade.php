<table id='dataTable' class='table table-hover align-items-center table-bordered'>
    <thead>
        <tr>
            <th scope='col'>Recibo</th>
            <th scope='col'>Periodo </th>
            <th scope='col'>Valor Nomin</th>
            <th scope='col'>Valor Aporte</th>
            <th scope='col'>Valor Interes</th>
            <th scope='col'>Valor Total</th>
            <th scope='col'>Indice %</th>
            <th scope='col'>Trabajador</th>
            <th scope='col'>Fecha Pago</th>
        </tr>
    </thead>
    <tbody class='list'>
        <? if (count($aportes) == 0): ?>
            <tr align='center'>
                <td colspan='9'>No hay datos para mostrar</td>
            </tr>
        <? else: ?>
            <? foreach ($aportes as $msubsi11): ?>
                <?
                $total = number_format($msubsi11['valapo'] + $msubsi11['valint'], 1, ",", ".");
                $valnom = number_format($msubsi11['valnom'], 1, ",", ".");
                $valapo = number_format($msubsi11['valapo'], 1, ",", ".");
                $valint = number_format($msubsi11['valint'], 1, ",", ".");
                if ($msubsi11['valnom'] != 0) {
                    $porapo = ($msubsi11['valapo'] * 100) / $msubsi11['valnom'];
                } else {
                    $porapo = 0;
                }
                ?>
                <tr>
                    <td><?= $msubsi11['numrad'] ?></td>
                    <td><?= $msubsi11['periodo'] ?></td>
                    <td>$ <?= $valnom ?></td>
                    <td>$ <?= $valapo ?></td>
                    <td>$ <?= $valint ?></td>
                    <td><?= $total ?></td>
                    <td><?= floor($porapo) ?></td>
                    <td><?= $msubsi11['cedtra'] ?></td>
                    <td><?= $msubsi11['fecrec'] ?></td>
                </tr>
            <? endforeach; ?>
        <? endif; ?>
    </tbody>
</table>