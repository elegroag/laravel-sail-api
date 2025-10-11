<?php

Tag::addJavascript('core/global');
?>
<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("consulta/reporte_auditoria", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-3 ml-auto">
                <div class="form-group">
                    <label for="tipopc" class="form-control-label">Tipo Opci&oacute;n</label>
                    <?php echo Tag::select("tipopc", $Mercurio09->find(), "using: tipopc,detalle", "dummyValue: ", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-2 ml-auto">
                <div class="form-group">
                    <label for="fecini" class="form-control-label">Fecha Inicial</label>
                    <?php echo Tag::calendar("fecini", "placeholder: Fecha Inicial", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                    <?php echo Tag::calendar("fecfin", "placeholder: Fecha Final", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-2 d-flex ml-auto">
                <button type="button" class="btn btn-primary align-self-center" onclick="consulta_auditoria();">Consultar</button>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-danger align-self-center" onclick="reporte_auditoria();">Reporte</button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>
<div id='consulta' class='table-responsive'></div>

<!-- Modal Captura -->
<div class="modal fade" id="capture-modal-info" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0">
                    <div class="card-header bg-secondary">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h3 class="mb-0"><?php echo "Información"; ?></h3>
                            </div>
                            <div class="col-2 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="result_info">
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function reporte_excel_carga_laboral() {
        window.location.href = Utils.getKumbiaURL(
            $Kumbia.controller + '/reporte_excel_carga_laboral',
        );
    }

    function reporte_excel_indicadores() {
        var validator = $('#form').validate({
            rules: {
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        window.location.href = Utils.getKumbiaURL(
            $Kumbia.controller +
            '/reporte_excel_indicadores/' +
            $('#fecini').val() +
            '/' +
            $('#fecfin').val(),
        );
    }

    function consulta_indicadores() {
        var validator = $('#form').validate({
            rules: {
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_indicadores'),
                data: {
                    fecini: $('#fecini').val(),
                    fecfin: $('#fecfin').val(),
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#consulta').html(response);
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }





    function info(tipopc, id) {
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/info'),
                data: {
                    tipopc: tipopc,
                    id: id,
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#result_info').html(response);
                $('#capture-modal-info').modal();
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }

    function consulta_activacion_masiva() {
        var validator = $('#form').validate({
            rules: {
                nit: {
                    required: true
                },
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_activacion_masiva'),
                data: {
                    nit: $('#nit').val(),
                    fecini: $('#fecini').val(),
                    fecfin: $('#fecfin').val(),
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#consulta').html(response);
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }

    function descarga_activacion(element) {
        window.open(Utils.getURL('temp/' + element.innerHTML));
    }
</script>

<?= Tag::javascriptInclude('Cajas/consulta/build.consulta'); ?>
