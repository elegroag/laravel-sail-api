import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

let validator = undefined;
let codofi_global = undefined;
window.App = $App;


const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            codofi: { required: true },
            detalle: { required: true },
            principal: { required: true },
            estado: { required: true },
        },
    });
};

$(function () {
    window.App.initialize();
    EventsPagination();

    $(document).on('blur', '#codofi', function () {
        validePk('#codofi');
    });

    const modalCaptureEl = document.getElementById('captureModal');
    const modalCapture = modalCaptureEl ? new bootstrap.Modal(modalCaptureEl) : null;
    const modalOpciones = new bootstrap.Modal(document.getElementById('captureOpciones'));
    const modalCiudades = new bootstrap.Modal(document.getElementById('captureCiudades'));

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const codofi = $(e.currentTarget).attr('data-cid');
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { codofi },
            callback: (response) => {
                if (!response) return Messages.display('No se pudieron cargar los datos', 'error');
                modalCapture.show();
                const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                $('#captureModalbody').html(tpl(response));

                $.each(response, function (key, value) {
                    $('#' + key.toString()).val(value);
                });
                validatorInit();
                $('#codofi').attr('disabled', 'true');
            },
            error: (xhr) => {
                Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        if (!$('#form').valid()) return;

        $('#form :input').each(function (elem) {
            $(this).removeAttr('disabled');
        });
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/guardar'),
            data: $('#form').serialize(),
            callback: (response) => {
                if (response['flag'] == true) {
                    buscar();
                    Messages.display(response['msg'], 'success');
                    if (modalCapture) modalCapture.hide();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const codofi = $(e.currentTarget).attr('data-cid');
        Swal.fire({
            title: 'Esta seguro de borrar?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success btn-fill',
            cancelButtonClass: 'btn btn-danger btn-fill',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                window.App.trigger('syncro', {
                    url: window.App.url(window.ServerController + '/borrar'),
                    data: { codofi },
                    callback: (response) => {
                        if (response['flag'] == true) {
                            buscar();
                            Messages.display(response['msg'], 'success');
                        } else {
                            Messages.display(response['msg'], 'error');
                        }
                    },
                    error: (xhr) => {
                        Messages.display('Error al borrar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                    }
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
        $('#captureModalbody').html(tpl({
            codofi: '',
            detalle: '',
            principal: '',
            estado: '',
        }));
        modalCapture.show();
        validatorInit();
    });

    $(document).on('click', "[data-toggle='reporte']", (e) => {
        e.preventDefault();
        const tipo = $(e.currentTarget).attr('data-type');
        window.location.href = window.App.url(window.ServerController + '/reporte/' + tipo);
    });

    $(document).on('click', "[data-toggle='opcion-view']", (e) => {
        e.preventDefault();
        const codofi = $(e.currentTarget).attr('data-cid');
        codofi_global = codofi;
        window.App.trigger('ajax', {
            url: window.App.url(window.ServerController + '/opcion_view'),
            data: { codofi },
            callback: (response) => {
                $('#captureOpcionesbody').html(response);
                modalOpciones.show();
                $('#form_opcion :input').each(function () {
                    if (this.type !== 'button') {
                        $(this).val('');
                        $(this).attr('disabled', false);
                    }
                });
            },
            error: (xhr) => {
                Messages.display('Error al cargar opciones: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='ciudad-view']", (e) => {
        e.preventDefault();
        const codofi = $(e.currentTarget).attr('data-cid');
        codofi_global = codofi;
        window.App.trigger('ajax', {
            url: window.App.url(window.ServerController + '/ciudad_view'),
            data: { codofi },
            callback: (response) => {
                $('#captureCiudadesbody').html(response);
                modalCiudades.show();
                $('#form_ciudad :input').each(function () {
                    if (this.type !== 'button') {
                        $(this).val('');
                        $(this).removeAttr('disabled');
                    }
                });
            },
            error: (xhr) => {
                Messages.display('Error al cargar ciudades: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='opcion-guardar']", (e) => {
        e.preventDefault();
        if (!$('#form_opcion').valid()) return;
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/guardarOpcion'),
            data: {
                codofi: codofi_global,
                tipopc: $('#tipopc_08').val(),
                usuario: $('#usuario_08').val(),
            },
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    $(document).find("[data-toggle='opcion-view'][data-cid='" + codofi_global + "']").trigger('click');
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar opción: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='opcion-borrar']", (e) => {
        e.preventDefault();
        const codofi = $(e.currentTarget).attr('data-codofi');
        const tipopc = $(e.currentTarget).attr('data-tipopc');
        const usuario = $(e.currentTarget).attr('data-usuario');
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/borrarOpcion'),
            data: { codofi, tipopc, usuario },
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    modalOpciones.hide();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al borrar opción: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='ciudad-borrar']", (e) => {
        e.preventDefault();
        const codofi = $(e.currentTarget).attr('data-codofi');
        const codciu = $(e.currentTarget).attr('data-codciu');
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/borrarCiudad'),
            data: { codofi, codciu },
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    modalCiudades.hide();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al borrar ciudad: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='ciudad-guardar']", (e) => {
        e.preventDefault();
        if (!$('#form_ciudad').valid()) return;
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/guardarCiudad'),
            data: {
                codofi: codofi_global,
                codciu: $('#codciu_05').val(),
            },
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar ciudad: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('blur', '#tipopc_08, #usuario_08', function () {
        if ($('#tipopc_08').val() == '') return;
        if ($('#usuario_08').val() == '') return;
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/validePkOpcion'),
            data: {
                codofi: codofi_global,
                tipopc: $('#tipopc_08').val(),
                usuario: $('#usuario_08').val(),
            },
            callback: (response) => {
                if (response['flag'] == false) {
                    Messages.display(response['msg'], 'warning');
                    $('#usuario_08').val('');
                    actualizar_select();
                }
            },
            error: (xhr) => {
                Messages.display('Error al validar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('blur', '#codciu_05', function () {
        if ($('#codciu_05').val() == '') return;
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/validePkCiudad'),
            data: {
                codofi: codofi_global,
                codciu: $('#codciu_05').val(),
            },
            callback: (response) => {
                if (response['flag'] == false) {
                    Messages.display(response['msg'], 'warning');
                    $('#codciu_05').val('');
                    $('#codciu_05').focus().select();
                }
            },
            error: (xhr) => {
                Messages.display('Error al validar ciudad: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });
});
