import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';

let generando = false;

const $form = () => $('#form_reportesol');
const $btn = () => $('#btn_generar_reporte');

const setButtonState = ({ disabled, loading = false }) => {
    const button = $btn();
    button.prop('disabled', disabled).attr('aria-disabled', disabled ? 'true' : 'false');

    const label = loading ? 'Generando Excel…' : 'Generar Excel';
    const icon = loading ? 'fas fa-spinner fa-spin' : 'fas fa-file-excel';

    button.find('[data-role="btn-label"]').text(label);
    button.find('i').attr('class', icon);
};

const syncButtonState = () => {
    const hasTipo = Boolean($('#tipo_solicitud').val());
    setButtonState({
        disabled: !hasTipo || generando,
        loading: generando,
    });
};

const showFeedback = (type, message) => {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    $('#reportesol-feedback')
        .html(`<div class="alert ${alertClass}"><i class="fas ${icon} mr-2" aria-hidden="true"></i>${message}</div>`)
        .addClass('is-visible');
};

const clearFeedback = () => {
    $('#reportesol-feedback').removeClass('is-visible').empty();
};

$(document).ready(() => {
    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
    });

    syncButtonState();

    $(document).on('change', '#tipo_solicitud', () => {
        clearFeedback();
        syncButtonState();
    });

    const form = $form();
    if (!form.length) {
        return;
    }

    form.validate({
        rules: {
            tipo: { required: true },
        },
        messages: {
            tipo: { required: 'Seleccione el tipo de solicitud.' },
        },
        errorClass: 'invalid-feedback d-block',
        errorElement: 'div',
        highlight(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight(element) {
            $(element).removeClass('is-invalid');
        },
        submitHandler(htmlForm) {
            clearFeedback();

            if (generando) {
                return false;
            }

            generando = true;
            syncButtonState();
            showFeedback('success', 'Se está generando el Excel. Si no inicia la descarga, revise el bloqueador de ventanas emergentes.');

            htmlForm.submit();

            // El submit abre otra pestaña; restaurar botón tras un breve lapso.
            window.setTimeout(() => {
                generando = false;
                syncButtonState();
            }, 1500);

            return false;
        },
        invalidHandler() {
            showFeedback('error', 'Complete el tipo de solicitud para generar el reporte.');
        },
    });
});
