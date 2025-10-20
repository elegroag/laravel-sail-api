import { $App } from '@/App';
import { Messages } from '@/Utils';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';

window.App = $App;
window.App.initialize();
let validator = undefined;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            fecini: { required: true },
            fecfin: { required: true },
        },
    });
}

const reporte_excel_carga_laboral = () => {
    window.location.href = Utils.getKumbiaURL(
        $Kumbia.controller + '/reporte_excel_carga_laboral',
    );
}

const reporte_excel_indicadores = () => {
    validatorInit();
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

const reporte_auditoria = () => {
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

const consulta_auditoria = () => {
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

const info = (tipopc, id) => {
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

const consulta_activacion_masiva = () => {
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

const descarga_activacion = (element) => {
    window.open(Utils.getURL('temp/' + element.innerHTML));
}

$(() => {

    flatpickr($('#fecini, #fecfin'), {
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: Spanish,
    });

    $(document).on('click', '[data-toggle="consulta_indicadores"]', function(e) {
        e.preventDefault();
        validatorInit();
        if (!$('#form').valid()) return;
        $App.trigger('ajax', {
            url: window.ServerController + '/consulta_indicadores',
            data: {
                fecini: $('#fecini').val(),
                fecfin: $('#fecfin').val(),
            },
            callback: (response) => {
                if(response && response.success === true){
                    $('#consulta').html(response.html);
                } else {
                    Messages.display(response, 'error');
                }
            },
            error: (response) => {
                Messages.display(response, 'error');
            }
        });
    });

    $(document).on('click', '[data-toggle="reporte_excel_indicadores"]', function(e) {
        e.preventDefault();
        reporte_excel_indicadores();
    });

    $(document).on('click', '[data-toggle="reporte_excel_carga_laboral"]', function(e) {
        e.preventDefault();
        reporte_excel_carga_laboral();
    });

    $(document).on('click', '[data-toggle="reporte_auditoria"]', function(e) {
        e.preventDefault();
        reporte_auditoria();
    });


    $(document).on('click', '[data-toggle="consulta_auditoria"]', function(e) {
        e.preventDefault();
        consulta_auditoria();
    });

    $(document).on('click', '[data-toggle="info"]', function(e) {
        e.preventDefault();
        info();
    });

    $(document).on('click', '[data-toggle="consulta_activacion_masiva"]', function(e) {
        e.preventDefault();
        consulta_activacion_masiva();
    });

    $(document).on('click', '[data-toggle="descarga_activacion"]', function(e) {
        e.preventDefault();
        descarga_activacion();
    });
});