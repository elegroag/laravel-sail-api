import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';

window.App = $App;
window.App.initialize();

/** tipopc en mercurio09 / config reportes */
const TIPOPC_EMPRESA = '2';
const TIPOPC_TRABAJADOR = '1';
const TIPOPC_CONYUGE = '3';
const TIPOPC_BENEFICIARIO = '4';
const TIPOPC_PENSIONADO = '9';
const TIPOPC_FACULTATIVO = '10';
const TIPOPC_INDEPENDIENTE = '11';

const TIPOPC_CON_DOCUMENTO_TRABAJADOR = new Set([
    TIPOPC_TRABAJADOR,
    TIPOPC_PENSIONADO,
    TIPOPC_FACULTATIVO,
    TIPOPC_INDEPENDIENTE,
]);

let validator;
let exportando = false;

const getTipopc = () => String($('#tipafis').val() || '');

const isTipoEmpresa = () => getTipopc() === TIPOPC_EMPRESA;

const isTipoConyuge = () => getTipopc() === TIPOPC_CONYUGE;

const isTipoBeneficiario = () => getTipopc() === TIPOPC_BENEFICIARIO;

const isTipoConDocumentoTrabajador = () => TIPOPC_CON_DOCUMENTO_TRABAJADOR.has(getTipopc());

const getCsrfToken = () => {
    const meta = document.querySelector("[name='csrf-token']");
    return meta ? meta.getAttribute('content') : $('input[name="_token"]').val();
};

const toggleCampo = ($campo, visible, inputSelector) => {
    if (visible) {
        $campo.removeClass('d-none');
        return;
    }

    $campo.addClass('d-none');
    $(inputSelector).val('');
};

const toggleCamposCondicionales = () => {
    toggleCampo($('#campo-nit-aportante'), isTipoEmpresa(), '#nit');
    toggleCampo($('#campo-cedtra-trabajador'), isTipoConDocumentoTrabajador(), '#cedtra');
    toggleCampo($('#campo-cedcon-conyuge'), isTipoConyuge(), '#cedcon');
    toggleCampo($('#campo-numdoc-beneficiario'), isTipoBeneficiario(), '#numdoc');
};

const syncExportButtonState = () => {
    const hasFechas = Boolean($('#fecini').val() && $('#fecfin').val());
    $('[data-toggle="exportar_reporte"]').prop('disabled', !hasFechas || exportando);
};

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            fecini: { required: true },
            fecfin: { required: true },
        },
    });
};

const appendIfPresent = (params, key, value) => {
    if (value) {
        params.set(key, value);
    }
};

const appendFormDataIfPresent = (formData, key, value) => {
    if (value) {
        formData.append(key, value);
    }
};

const collectFormData = () => {
    const params = new URLSearchParams();
    params.set('fecini', $('#fecini').val());
    params.set('fecfin', $('#fecfin').val());

    if (isTipoEmpresa()) {
        appendIfPresent(params, 'nit', $('#nit').val());
    }

    if (isTipoConDocumentoTrabajador()) {
        appendIfPresent(params, 'cedtra', $('#cedtra').val());
    }

    if (isTipoConyuge()) {
        appendIfPresent(params, 'cedcon', $('#cedcon').val());
    }

    if (isTipoBeneficiario()) {
        appendIfPresent(params, 'numdoc', $('#numdoc').val());
    }

    appendIfPresent(params, 'tipafis', $('#tipafis').val());

    return params;
};

const buildExportFormData = () => {
    const formData = new FormData();
    formData.append('_token', getCsrfToken());
    formData.append('fecini', $('#fecini').val());
    formData.append('fecfin', $('#fecfin').val());

    if (isTipoEmpresa()) {
        appendFormDataIfPresent(formData, 'nit', $('#nit').val());
    }

    if (isTipoConDocumentoTrabajador()) {
        appendFormDataIfPresent(formData, 'cedtra', $('#cedtra').val());
    }

    if (isTipoConyuge()) {
        appendFormDataIfPresent(formData, 'cedcon', $('#cedcon').val());
    }

    if (isTipoBeneficiario()) {
        appendFormDataIfPresent(formData, 'numdoc', $('#numdoc').val());
    }

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

const mostrarResumen = (payload) => {
    const resumen = payload.resumen || {};
    $('#resumen_total').text(resumen.total ?? 0);
    $('#resumen_en_termino').text(resumen.en_termino ?? 0);
    $('#resumen_vencido').text(resumen.vencido ?? 0);
    $('#resumen_en_tramite').text(resumen.en_tramite ?? 0);
    $('#resumen_nota').text(
        `Umbral de oportunidad: ${payload.umbral_dias ?? '-'} dias habiles.`
    );
    $('#resumen').removeClass('d-none');
    syncExportButtonState();
};

const previsualizarReporte = async () => {
    validatorInit();
    if (!$('#form').valid()) {
        return;
    }

    const url = `${window.ReporteOportunidadRoutes?.previsualizar}?${collectFormData().toString()}`;

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('No fue posible previsualizar el reporte.');
        }

        const payload = await response.json();
        mostrarResumen(payload);
    } catch (error) {
        window.alert(error.message || 'Error al previsualizar el reporte.');
    }
};

const exportarReporte = async () => {
    validatorInit();
    if (!$('#form').valid()) {
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

        const blob = await response.blob();
        downloadBlob(blob, extractFilename(response.headers.get('Content-Disposition')));
    } catch (error) {
        window.alert(error.message || 'Error al exportar el reporte.');
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

    toggleCamposCondicionales();
    syncExportButtonState();

    $(document).on('change', '#tipafis', toggleCamposCondicionales);
    $(document).on('change input', '#fecini, #fecfin', syncExportButtonState);

    $(document).on('click', '[data-toggle="previsualizar_reporte"]', (event) => {
        event.preventDefault();
        previsualizarReporte();
    });

    $(document).on('click', '[data-toggle="exportar_reporte"]', (event) => {
        event.preventDefault();
        exportarReporte();
    });
});
