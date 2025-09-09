<div class="card-body">
    <div class="nav-wrapper">
        <ul class="nav nav-pills" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-bs-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>SALDO PENDIENTE COBRAR
                </a>
            </li>
        </ul>
    </div>
    <div class="card shadow">
        <div class="card-body pt-0">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="row">
                        <table class="table align-items-center table-bordered">
                            <thead class="bg-green-blue">
                                <tr>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Quien recibe cuota</th>
                                    <th scope="col">Parentesco</th>
                                    <th scope="col">Giro</th>
                                    <th scope="col">Abonos (Fecha - Valor - Periodo Giro)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $saldo_pendiente = 0;
                                if (count($saldos) > 0) {

                                    foreach ($saldos as $msaldo) {
                                        $row_abonos = '';
                                        if (count($msaldo['abonos']) > 0) {
                                            $row_abonos .= "<table class='table table-hover align-items-center table-bordered'><tbody>";
                                            foreach ($msaldo['abonos'] as $mabono) {
                                                $saldo_pendiente += $mabono['valor_abono'];
                                                $valor_abono = number_format($mabono['valor_abono'], 2, ',', '.');
                                                $row_abonos .= "<tr><td style='width: 50%'>{$mabono['fecha']}</td><td style='width: 25%'>$ {$valor_abono}</td><td style='width: 25%'>{$mabono['periodo_giro']}</td></tr>";
                                            }
                                            $row_abonos .= "</tbody></table>";
                                        }

                                        $giro = $msaldo['giro'] == 'S' ? 'SI' : '';
                                        $giro = $msaldo['giro'] == 'N' ? 'NO' : $giro;
                                        echo "<tr>
                                        <td>{$msaldo['documento']}</td>
                                        <td>{$msaldo['prinom']} {$msaldo['segnom']} {$msaldo['priape']} {$msaldo['segape']}</td>
                                        <td>{$msaldo['parent']}</td>
                                        <td>{$giro}</td>
                                        <td>{$row_abonos}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr align='center'>";
                                    echo "<td colspan=4>No hay datos para mostrar</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <hr />
                        <div class="row">
                            <div class="col-6">
                                <p>Saldo pendiente por cobrar: $ <?php echo number_format($saldo_pendiente, 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= Tag::javascriptInclude('Mercurio/consultastrabajador/consultastrabajador.build'); ?>

<?
/*
'documento' => $salcon->cedcon,
'coddoc' => $salcon->coddoc,
'priape' => $salcon->priape,
'segape' => $salcon->segape,
'prinom' => $salcon->prinom,
'segnom' => $salcon->segnom,
'parent' => 'Conyuge',
'giro' => '',
'fecafi' => $salcon->fecafi,
'abonos'  => $saldos_abonados
*/