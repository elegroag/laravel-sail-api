<?php

Tag::addJavascript('core/global');
?>

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("reportes/reporte_auditoria", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="fecini" class="form-control-label">Fecha Inicial</label>
                    <?php echo Tag::calendar("fecini", "placeholder: Fecha Inicial", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                    <?php echo Tag::calendar("fecfin", "placeholder: Fecha Final", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-primary align-self-center" onclick="consulta_indicadores();">Consultar</button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<div id='consulta' class='table-responsive'>
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

    function reporte_auditoria() {
        var validator = $('#form').validate({
            rules: {
                tipopc: {
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
        $('#form').submit();
    }

    function consulta_auditoria() {
        var validator = $('#form').validate({
            rules: {
                tipopc: {
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
                url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_auditoria'),
                data: {
                    tipopc: $('#tipopc').val(),
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
