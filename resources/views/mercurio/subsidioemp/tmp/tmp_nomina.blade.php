<table id='dataTable' class='table table-hover align-items-center table-bordered'>
    <thead>
        <tr>
            <th scope='col'>Cedula </th>
            <th scope='col'>Nombre</th>
            <th scope='col'>Porcentaje Aporte</th>
            <th scope='col'>Dias Trabajados</th>
            <th scope='col'>Salario</th>
            <th scope='col'>Ibc</th>
            <th scope='col'>Valor Aporte</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='INGRESO'>Ing</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='RETIRO'>Ret</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL TEMPORAL'>VST</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL PERMANENTE'>VSP</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='SUSPENCION TEMPORAL CONTRATO'>STC</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ENFERMEDAD'>ITE</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='LICENCIA MATERNIDAD'>LM</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='VACACIONES'>VAC</th>
            <th scope='col' data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ACCIDENTE DE TRABAJO'>ITAT</th>
        </tr>
    </thead>
    <tbody class='list'>
        <? if (count($nominas) == 0): ?>
            <tr align='center'>
                <td colspan='16'>No hay datos para mostrar</td>
            </tr>
        <? else: ?>
            <? foreach ($nominas as $msubsi65): ?>
                <?
                $valnom = number_format($msubsi65['valnom'], 1, ",", ".");
                $valapo = number_format($msubsi65['valapo'], 1, ",", ".");
                $salbas = number_format($msubsi65['salbas'], 1, ",", ".");
                ?>
                <tr>
                    <td><?= $msubsi65['cedtra'] ?></td>
                    <td><?= $msubsi65['prinom'] . ' ' . $msubsi65['segnom'] . ' ' . $msubsi65['priape'] . ' ' . $msubsi65['segape'] ?> </td>
                    <td><?= $msubsi65['tarapo'] ?></td>
                    <td><?= $msubsi65['diatra'] ?></td>
                    <td><?= $salbas ?></td>
                    <td><?= $valnom ?></td>
                    <td><?= $valapo ?></td>
                    <td data-bs-toggle='tooltip' data-placement='top' title='INGRESO'><?= $msubsi65['ingtra'] ?></td>
                    <td data-bs-toggle='tooltip' data-placement='top' title='RETIRO'><?= $msubsi65['novret'] ?></td>
                    <td data-bs-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL TEMPORAL'><?= $msubsi65['novvps'] ?></td>
                    <td data-bs-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL PERMANENTE'><?= $msubsi65['novvts'] ?></td>
                    <td data-bs-toggle='tooltip' data-placement='top' title='SUSPENCION TEMPORAL CONTRATO'><?= $msubsi65['novstc'] ?></td>
                    <td data-bs-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ENFERMEDAD'><?= $msubsi65['novitg'] ?></td>
                    <td data-bs-toggle='tooltip' title='LICENCIA MATERNIDAD'><?= $msubsi65['licnom'] ?></td>
                    <td data-bs-toggle='tooltip' title='VACACIONES'><?= $msubsi65['vacnom'] ?></td>
                    <td data-bs-toggle='tooltip' title='INCAPACIDAD TEMPORAL ACCIDENTE DE TRABAJO'><?= $msubsi65['incnom'] ?></td>
                </tr>
            <? endforeach; ?>
        <? endif; ?>
    </tbody>
</table>