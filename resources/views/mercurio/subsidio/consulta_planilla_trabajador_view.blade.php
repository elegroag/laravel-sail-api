<?php
echo View::getContent();
echo TagUser::help($title, $help);
?>
<?= Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css') ?>
<?= Tag::Assets('datatables.net/js/dataTables.min', 'js') ?>
<?= Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js') ?>

<style>
    #dataTable {
        font-size: 0.7rem;
    }

    #dataTable thead {
        background-color: #f0f0f0;
    }

    #dataTable th {
        padding: 0.3rem;
        text-align: left;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    #dataTable td {
        padding: 0.3rem;
        text-align: center;
        vertical-align: middle;
    }
</style>

<script type="text/template" id="templatePlanillas">
    <table id='dataTable' class='table table-hover align-items-center table-bordered'>
        <thead>
            <tr>
                <th scope='col'>Razon Social</th>
                <th scope='col'>Periodo Aporte</th>
                <th scope='col'>Indice Aporte</th>
                <th scope='col'>Salario Base</th>
                <th scope='col'>Dias Trabajados</th>
                <th scope='col'>Fecha Pago</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='INGRESO'>Ing</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='RETIRO'>Ret</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL TEMPORAL'>VST</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='SUSPENCION TEMPORAL CONTRATO'>STC</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ENFERMEDAD'>ITE</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='LICENCIA MATERNIDAD'>LM</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='VACACIONES'>VAC</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ACCIDENTE DE TRABAJO'>ITAT</th>
                <th scope='col' data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL PERMANENTE'>VSP</th>
            </tr>
        </thead>
        <tbody class='list'>
            <% if (planilla.length == 0) { %>
                <tr align='center'>
                    <td colspan=15><label class='text-center'>No hay datos para mostrar</label></td>
                </tr>
            <% } else { 
                _.each(planilla, function (item) {
                %>
                <tr>
                    <td><%=item.razsoc%></td>
                    <td><%=item.perapo%></td>
                    <td><%=item.tarapo%></td>
                    <td><%=item.salbas%></td>
                    <td><%=item.diatra%></td>
                    <td><%=item.fecrec%></td>
                    <td data-toggle='tooltip' data-placement='top' title='INGRESO' ><%=item.ingtra%></td>
                    <td data-toggle='tooltip' data-placement='top' title='RETIRO'><%=item.novret%></td>
                    <td data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL TEMPORAL'><%=item.novvps%></td>
                    <td data-toggle='tooltip' data-placement='top' title='SUSPENCION TEMPORAL CONTRATO'><%=item.novstc%></td>
                    <td data-toggle='tooltip' data-placement='top' title='INCAPACIDAD TEMPORAL ENFERMEDAD'><%=item.novitg%></td>
                    <td data-toggle='tooltip' title='LICENCIA MATERNIDAD'><%=item.licnom%></td>
                    <td data-toggle='tooltip' title='VACACIONES'><%=item.vacnom%></td>
                    <td data-toggle='tooltip' title='INCAPACIDAD TEMPORAL ACCIDENTE DE TRABAJO'><%=item.incnom%></td>
                    <td data-toggle='tooltip' data-placement='top' title='VARIACION SALARIAL PERMANENTE'><%=item.novvts%></td>
                </tr>
                <% }); %>
            <% } %>
        </tbody>
    </table>
</script>

<div class="card mb-0">
    <div class="card-header bg-green-blue p-1">
        <div class="btn-group w-100">
            <button type="button" class="btn btn-default w-10" id='bt_consulta_planilla_trabajador'><i class="fa fa-search"></i> Consultar</button>
        </div>
    </div>
    <div class="card-body">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="perini" class="form-control-label">Periodo Inicial</label>
                        <?php echo TagUser::periodo("perini", "placeholder: Periodo Inicial", "class: form-control", "value: " . date('Ym', strtotime('-3 month'))); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="perfin" class="form-control-label">Periodo Final</label>
                        <?php echo TagUser::periodo("perfin", "placeholder: Periodo Final", "class: form-control", "value: " . date('Ym')); ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<div id='consulta' class='table-responsive'></div>

<?= Tag::javascriptInclude('Mercurio/consultastrabajador/consultastrabajador.build'); ?>