import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

const emptyForm = () => ({
    codapl: '',
    webser: '',
    path: '',
    urlonl: '',
    puncom: '',
});

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            codapl: { required: false },
            webser: { required: false },
            path: { required: false },
            urlonl: { required: false },
            puncom: { required: false },
        },
    });
};

const renderForm = (data = emptyForm()) => {
    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
    $('#captureModalbody').html(tpl(data));
};

const focusField = (selector) => {
    setTimeout(() => {
        $(selector).focus().select();
    }, 300);
};

$(() => {
    window.App.initialize();
    const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
    EventsPagination();

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const codapl = $(e.currentTarget).attr('data-cid');

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { codapl },
            callback: (response) => {
                if (!response) {
                    Messages.display('No fue posible cargar el registro.', 'error');
                    return;
                }

                renderForm(response);
                $.each(response, (key, value) => {
                    $('#' + key).val(value);
                });
                $('#codapl').attr('disabled', true);
                modalCapture.show();
                focusField('#webser');
                validatorInit();
            },
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        if (!$('#form').valid()) {
            return;
        }

        $('#form :input').each(function () {
            $(this).removeAttr('disabled');
        });

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/guardar'),
            data: $('#form').serialize(),
            callback: (response) => {
                if (response && response.flag === true) {
                    buscar();
                    Messages.display(response.msg, 'success');
                    modalCapture.hide();
                } else {
                    Messages.display(response?.msg || 'No fue posible guardar el registro.', 'error');
                }
            },
        });
    });

    $(document).on('click', "[data-toggle='header-nuevo']", (e) => {
        e.preventDefault();
        renderForm(emptyForm());
        $('#form :input').each(function () {
            $(this).val('');
            $(this).removeAttr('disabled');
        });
        modalCapture.show();
        focusField('#codapl');
        validatorInit();
    });
});
