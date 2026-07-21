import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';

window.App = $App;
window.App.initialize();

let validator;
let exportando = false;

const getCsrfToken = () => {
    const meta = document.querySelector("[name='csrf-token']");
    return meta ? meta.getAttribute('content') : $('input[name="_token"]').val();
};

const $exportBtn = () => $('[data-toggle="exportar_reporte"]');

const setExportButtonState = ({ disabled, loading = false }) => {
    const $btn = $exportBtn();
    $btn.prop('disabled', disabled).attr('aria-disabled', disabled ? 'true' : 'false');

    const label = loading ? 'Generando Excel…' : 'Descargar Excel';
    const icon = loading ? 'fas fa-spinner fa-spin' : 'fas fa-file-excel';

    $btn.find('[data-role="btn-label"]').text(label);
    $btn.find('i').attr('class', icon);
};

const syncExportButtonState = () => {
    const hasFechas = Boolean($('#fecini').val() && $('#fecfin').val());
    setExportButtonState({
        disabled: !hasFechas || exportando,
        loading: exportando,
    });
};

const showFeedback = (type, message) => {
    const $box = $('#oportunidad-feedback');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    $box
        .html(`<div class="alert ${alertClass}"><i class="fas ${icon} mr-2" aria-hidden="true"></i>${message}</div>`)
        .addClass('is-visible');
};

const clearFeedback = () => {
    $('#oportunidad-feedback').removeClass('is-visible').empty();
};

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            fecini: { required: true },
            fecfin: { required: true },
        },
        messages: {
            fecini: { required: 'La fecha inicial es obligatoria.' },
            fecfin: { required: 'La fecha final es obligatoria.' },
        },
        errorClass: 'invalid-feedback d-block',
        errorElement: 'div',
        highlight(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight(element) {
            $(element).removeClass('is-invalid');
        },
    });
};

const buildExportFormData = () => {
    const formData = new FormData();
    formData.append('_token', getCsrfToken());
    formData.append('fecini', $('#fecini').val());
    formData.append('fecfin', $('#fecfin').val());

    const tipafi = $('#tipafis').val();
    if (tipafi) {
        formData.append('tipafis[]', tipafi);
    }

    return formData;
};

const extractFilename = (contentDisposition) => {
    if (!contentDisposition) {
        return `control_oportunidad_afiliacion_${Date.now()}.xlsx`;
    }

    const match = contentDisposition.match(/filename="?([^";]+)"?/i);
    return match?.[1] || `control_oportunidad_afiliacion_${Date.now()}.xlsx`;
};

const downloadBlob = (blob, filename) => {
    const url = window.URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.download = filename;
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    window.URL.revokeObjectURL(url);
};

const exportarReporte = async () => {
    clearFeedback();
    validatorInit();
    if (!$('#form').valid()) {
        showFeedback('error', 'Complete las fechas requeridas para generar el reporte.');
        return;
    }

    const action = window.ReporteOportunidadRoutes?.exportar;
    if (!action || exportando) {
        return;
    }

    exportando = true;
    syncExportButtonState();

    try {
        const response = await fetch(action, {
            method: 'POST',
            body: buildExportFormData(),
            credentials: 'same-origin',
            headers: {
                Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const contentType = response.headers.get('Content-Type') || '';

        if (!response.ok) {
            if (contentType.includes('application/json')) {
                const payload = await response.json();
                const message = payload.message
                    || Object.values(payload.errors || {}).flat().join('\n')
                    || 'No fue posible exportar el reporte.';
                throw new Error(message);
            }

            const text = await response.text();
            throw new Error(text || 'No fue posible exportar el reporte.');
        }

        const filename = extractFilename(response.headers.get('Content-Disposition'));
        const blob = await response.blob();
        downloadBlob(blob, filename);
        showFeedback('success', `El archivo <strong>${filename}</strong> se descargó correctamente.`);
    } catch (error) {
        showFeedback('error', error.message || 'Error al exportar el reporte.');
    } finally {
        exportando = false;
        syncExportButtonState();
    }
};

$(document).ready(() => {
    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: syncExportButtonState,
    });

    syncExportButtonState();

    $(document).on('change input', '#fecini, #fecfin', syncExportButtonState);

    $(document).on('click', '[data-toggle="exportar_reporte"]', (event) => {
        event.preventDefault();
        exportarReporte();
    });
});
