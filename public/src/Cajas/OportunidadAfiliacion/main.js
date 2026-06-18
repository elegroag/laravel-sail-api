import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';

window.App = $App;
window.App.initialize();

let validator;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            modalidad: { required: true },
            fecini: { required: true },
            fecfin: { required: true },
        },
    });
};

const generarReporte = () => {
    validatorInit();
    if (!$('#form').valid()) {
        return;
    }

    const modalidad = $('#modalidad').val();
    const action = window.ReporteOportunidadRoutes?.[modalidad];

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
    $form.append($('<input>', { type: 'hidden', name: 'modalidad', value: modalidad }));
    $form.append($('<input>', { type: 'hidden', name: 'campo_fecha', value: $('#campo_fecha').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'fecini', value: $('#fecini').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'fecfin', value: $('#fecfin').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'estado', value: $('#estado').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'nit', value: $('#nit').val() }));
    $form.append($('<input>', { type: 'hidden', name: 'cedtra', value: $('#cedtra').val() }));

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

    $(document).on('click', '[data-toggle="generar_reporte"]', (event) => {
        event.preventDefault();
        generarReporte();
    });
});
