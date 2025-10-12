$(() => {

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

});
