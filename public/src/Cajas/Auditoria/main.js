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
    let headers = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Fecsol', 'Fecapr', 'Radicado', 'Días'];
    if (hasExtra) {
        headers.push('Extra');
    }
    headers.push('Estado');
    headers.push('Acciones');

    let thead = '<thead><tr>';
    for (const h of headers) {
        thead += `<th>${h}</th>`;
    }
    thead += '</tr></thead>';

    const tipopc = $('#tipopc').val();

    let rows = data.map(item => {
        let cells = [
            item.documento,
            item.nombre,
            item.responsable,
            item.fecha,
            item.fecsol,
            item.fecapr,
            item.radicado,
            item.dias_vencidos,
        ];
        if (hasExtra && item.extra) {
            cells.push(item.extra);
        }
        cells.push(item.estado);
        cells.push(`
            <button type="button"
                class="btn btn-info btn-sm btn-detail"
                data-toggle="audit-detail"
                data-id="${item.id}"
                data-tipopc="${tipopc}">
                <i class="fa fa-eye"></i> Ver detalle
            </button>`);

        return '<tr>' + cells.map(c => `<td>${c ?? ''}</td>`).join('') + '</tr>';
    }).join('');

    const csrfToken = document.querySelector("[name='csrf-token']")
        ? document.querySelector("[name='csrf-token']").getAttribute('content')
        : '';

    const exportUrl = $('#consulta').data('export-url') || `${window.ServerController}/exportar`;

    const exportForm = `
<form id="form_exportar_auditoria" action="${exportUrl}" method="POST" target="_blank" class="d-inline-block">
    <input type="hidden" name="_token" value="${csrfToken}">
    <input type="hidden" name="tipopc" value="${$('#tipopc').val()}">
    <input type="hidden" name="fecini" value="${$('#fecini').val()}">
    <input type="hidden" name="fecfin" value="${$('#fecfin').val()}">
    <input type="hidden" name="format" value="xlsx">
    <button type="submit" class="btn btn-success btn-sm">
        <i class="fa fa-file-excel-o"></i> Exportar Excel
    </button>
</form>`;

    const toolbar = `
<div class="d-flex justify-content-end mt-2 mb-2">
    ${exportForm}
</div>`;

    return `<div class="table-responsive mt-2">
${toolbar}
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
    const auditDetailModal = new bootstrap.Modal(document.getElementById('auditDetailModal'));

    $(document).on('click', "[data-toggle='consulta']", (e) => {
        e.preventDefault();
        consulta_auditoria();
    });

    $(document).on('click', "[data-toggle='reporte']", (e) => {
        e.preventDefault();
        reporte_auditoria();
    });

    $(document).on('click', '[data-toggle="audit-detail"]', (e) => {
        e.preventDefault();
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        const tipopc = btn.dataset.tipopc;

        $('#auditDetailModalbody').html('<div class="p-4 text-center text-muted">Cargando...</div>');
        auditDetailModal.show();

        window.App.trigger('ajax', {
            url: `${window.ServerController}/infor`,
            data: { tipopc, id },
            callback: (response) => {
                if (response && response.success === true) {
                    $('#auditDetailModalbody').html(response.html || '<div class="alert alert-info m-3">Sin información disponible.</div>');
                } else {
                    $('#auditDetailModalbody').html(
                        `<div class="alert alert-danger m-3">${response?.msj || 'No fue posible cargar el detalle.'}</div>`
                    );
                }
            },
            error: () => {
                $('#auditDetailModalbody').html(
                    '<div class="alert alert-danger m-3">No fue posible cargar el detalle.</div>'
                );
            },
        });
    });

});
