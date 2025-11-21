import { $App } from '@/App';
import { LayoutGeneral } from '@/Cajas/LayoutGeneral';
import { Region } from '@/Common/Region';
import loading from '@/Componentes/Views/Loading';
import { Messages } from '@/Utils';
import { aplicarFiltro, EventsPagination } from '../Glob/Glob';

window.App = $App;
let validator;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            coddoc: { required: true },
            tipopc: { required: true },
            obliga: { required: true },
            nota: { required: false },
            auto_generado: { required: true },
        },
    });
};

$(() => {
    window.App.initialize();
    EventsPagination();

    const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
    const region = new Region({ el: '#boneLayout' });
    const layout = new LayoutGeneral();
    region.show(layout);

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const coddoc = $(e.currentTarget).attr('data-coddoc');
        const tipopc = $(e.currentTarget).attr('data-tipopc');
        loading.show();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: {
                coddoc: coddoc,
                tipopc: tipopc,
            },
            callback: (response) => {
                loading.hide();
                if (response.success == true) {
                    modalCapture.show();
                    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl(response.data));
                    validatorInit();

                    $.each(response.data, (key, value) => {
                        $('#' + key.toString()).val(value);
                    });

                    $('#coddoc').attr('disabled', 'true');
                    $('#tipopc').attr('disabled', 'true');
                    $('#tipsoc').attr('disabled', 'true');
                }
            },
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();

        $('#coddoc').removeAttr('disabled');
        $('#tipopc').removeAttr('disabled');
        if (!validator.valid()) return;

        loading.show();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/guardar'),
            data: {
                coddoc: $('#coddoc').val(),
                tipopc: $('#tipopc').val(),
                obliga: $('#obliga').val(),
                nota: $('#nota').val(),
                auto_generado: $('#auto_generado').val(),
            },
            callback: (response) => {
                loading.hide();
                if (response.success == true) {
                    Messages.display(response.msj, 'success');
                    modalCapture.hide();
                    aplicarFiltro();
                } else {
                    Messages.display(response.msj, 'error');
                }
            },
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const coddoc = $(e.currentTarget).attr('data-coddoc');
        const tipopc = $(e.currentTarget).attr('data-tipopc');
        Swal.fire({
            title: 'Confirmar!',
            html: 'Esta seguro de borrar el registro seleccionado ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success btn-fill',
            cancelButtonClass: 'btn btn-danger btn-fill',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.isConfirmed) {
                window.App.trigger('syncro', {
                    url: window.App.url(window.ServerController + '/borrar'),
                    data: {
                        coddoc: coddoc,
                        tipopc: tipopc,
                    },
                    callback: (response) => {
                        if (response.success == true) {
                            Messages.display(response.msj, 'success');
                            aplicarFiltro();
                        } else {
                            Messages.display(response.msj, 'error');
                        }
                    },
                });
            }
        });
    });

    $(document).on('click', "[data-toggle='header-nuevo']", (e) => {
        e.preventDefault();
        $('#form :input').each(function (elem) {
            $(this).val('');
            $(this).removeAttr('disabled');
        });

        const tpl = _.template(document.getElementById('tmp_form').innerHTML);
        $('#captureModalbody').html(
            tpl({
                coddoc: '',
                tipopc: '',
                obliga: '',
                nota: '',
                auto_generado: '',
            }),
        );
        modalCapture.show();
        validatorInit();
    });
});
