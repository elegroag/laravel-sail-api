import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
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

const buildAuditoriaTable = (data, hasExtra) => {
    let headers = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Días'];
    if (hasExtra) {
        headers.push('Extra');
    }
    headers.push('Estado');

    let thead = '<thead><tr>';
    for (const h of headers) {
        thead += `<th>${h}</th>`;
    }
    thead += '</tr></thead>';

    let rows = data.map(item => {
        let cells = [
            item.documento,
            item.nombre,
            item.responsable,
            item.fecha,
            item.dias_vencidos,
        ];
        if (hasExtra && item.extra) {
            cells.push(item.extra);
        }
        cells.push(item.estado);

        return '<tr>' + cells.map(c => `<td>${c ?? ''}</td>`).join('') + '</tr>';
    }).join('');

    return `<div class="table-responsive mt-2">
<table class="table table-striped table-bordered datatable-auditoria" id="tabla-auditoria">
${thead}
<tbody>${rows}</tbody>
</table>
</div>`;
};

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
            if (response && response.data) {
                $('#consulta').html(buildAuditoriaTable(response.data, response.hasExtra));
            } else {
                $('#consulta').html('<div class="alert alert-info">No se encontraron resultados</div>');
            }
        },
        error: (jqXHR, textStatus) => {
            alert('Request failed: ' + textStatus);
        }
    });
};

const reporte_auditoria = () => {
    validatorInit();
    if (!$('#form').valid()) {
        return;
    }
    $('#form').submit();
};

$(() => {
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
        locale: Spanish,
        allowInput: true,
        disableTouchKeyboard: true,
        altInput: true,
        altFormat: 'd/m/Y',
    });

    window.App.initialize();
    const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));

    $(document).on('click', "[data-toggle='consulta']", (e) => {
        e.preventDefault();
        consulta_auditoria();
    });

    $(document).on('click', "[data-toggle='reporte']", (e) => {
        e.preventDefault();
        reporte_auditoria();
    });

});
