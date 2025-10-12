import { $App } from '@/App';
import { Messages } from '@/Utils';

window.App = $App;
let validator;

const validatorInit = () => {
    validator = $('#form').validate({
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
};


$(() => {
	window.App.initialize();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));

    const consulta_auditoria = () => {
        validatorInit();
        if (!$('#form').valid()) {
            return;
        }
        window.App.trigger('ajax', {
            url: window.ServerController + '/consulta',
            data: {
                tipopc: $('#tipopc').val(),
                fecini: $('#fecini').val(),
                fecfin: $('#fecfin').val(),
            },
            callback: (response) => {
                $('#consulta').html(response);
            },
            error: (jqXHR, textStatus) => {
                alert('Request failed: ' + textStatus);
            }
        })
    }

    const reporte_auditoria = () => {
        validatorInit();
        if (!$('#form').valid()) {
            return;
        }
        $('#form').submit();
    }

	$(document).on('click', "[data-toggle='consulta']", (e) => {
		e.preventDefault();
        consulta_auditoria();
	});

    $(document).on('click', "[data-toggle='reporte']", (e) => {
		e.preventDefault();
        reporte_auditoria();
	});


});
