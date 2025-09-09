
<script type="text/template" id="templateNoGiro">
    <table class='table table-hover align-items-center table-bordered'>
       <thead>
            <tr>
                <th scope='col'>Periodo Girado</th>
                <th scope='col'>Periodo Pagado</th>
                <th scope='col'>Razon Social</th>
                <th scope='col'>Nombre Beneficiario</th>
                <th scope='col'>Motivo</th>
            </tr>
        </thead>
        <tbody class='list'>
            <% if (motivos.length == 0) { %>
                <tr align='center'>
                    <td colspan=10><label class='text-center'>No hay datos para mostrar</label></td>
                </tr>
            <% } else { %>
                <% _.each(motivos, function(item) { %>
                    <tr>
                        <td><%= item.pergir %></td>
                        <td><%= item.periodo %></td>
                        <td><%= item.razsoc %></td>
                        <td><%= item.nombre %></td>
                        <td><%= item.motivo %></td>
                    </tr>
                <% }) %>
            <% } %>
        </tbody>
    </table>
</script>

<div class="card mb-0">
    <div class="card-header bg-green-blue p-1">
        <div class="btn-group w-100">
            <button type="button" class="btn btn-default w-10" id='bt_consulta_nogiro'><i class="fa fa-search"></i> Consultar</button>
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
