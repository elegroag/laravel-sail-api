import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';

window.App = $App;
window.App.initialize();

let validator;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            fecini: { required: true },
            fecfin: { required: true },
        },
    });
};

const collectFormData = () => {
    const params = new URLSearchParams();
    params.set('fecini', $('#fecini').val());
    params.set('fecfin', $('#fecfin').val());

    const estado = $('#estado').val();
    if (estado) {
        params.set('estado', estado);
    }

    const nit = $('#nit').val();
    if (nit) {
        params.set('nit', nit);
    }

    const cedtra = $('#cedtra').val();
    if (cedtra) {
        params.set('cedtra', cedtra);
    }

    if ($('#solo_vencidos').is(':checked')) {
        params.set('solo_vencidos', '1');
    }

    if ($('#solo_pendientes').is(':checked')) {
        params.set('solo_pendientes', '1');
    }

    const tipafis = $('#tipafis').val() || [];
    tipafis.forEach((value) => {
        params.append('tipafis[]', value);
    });

    return params;
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
    $('[data-toggle="exportar_reporte"]').prop('disabled', (resumen.total ?? 0) === 0);
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

const exportarReporte = () => {
    validatorInit();
    if (!$('#form').valid()) {
        return;
    }

    const action = window.ReporteOportunidadRoutes?.exportar;
    if (!action) {
        return;
    }

    const $form = $('<form>', {
        method: 'POST',
        action,
        target: '_blank',
    });

    const csrfToken = document.querySelector("[name='csrf-token']")
        ? document.querySelector("[name='csrf-token']").getAttribute('content')
        : $('input[name="_token"]').val();

    $form.append($('<input>', { type: 'hidden', name: '_token', value: csrfToken }));
    $form.append($('<input>', { type: 'hidden', name: 'fecini', value: $('#fecini').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'fecfin', value: $('#fecfin').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'estado', value: $('#estado').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'nit', value: $('#nit').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'cedtra', value: $('#cedtra').val() }));

    if ($('#solo_vencidos').is(':checked')) {
        $form.append($('<input>', { type: 'hidden', name: 'solo_vencidos', value: '1' }));
    }

    if ($('#solo_pendientes').is(':checked')) {
        $form.append($('<input>', { type: 'hidden', name: 'solo_pendientes', value: '1' }));
    }

    const tipafis = $('#tipafis').val() || [];
    tipafis.forEach((value) => {
        $form.append($('<input>', { type: 'hidden', name: 'tipafis[]', value }));
    });

    $('body').append($form);
    $form.trigger('submit');
    $form.remove();
};

$(document).ready(() => {
    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
    });

    $(document).on('click', '[data-toggle="previsualizar_reporte"]', (event) => {
        event.preventDefault();
        previsualizarReporte();
    });

    $(document).on('click', '[data-toggle="exportar_reporte"]', (event) => {
        event.preventDefault();
        exportarReporte();
    });
});
