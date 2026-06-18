import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';

$(document).ready(() => {
    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
    });

    const $form = $('#form_reportesol');
    if ($form.length) {
        $form.validate({
            rules: {
                tipo: { required: true },
            },
            submitHandler: function (form) {
                form.submit();
            },
        });
    }
});
