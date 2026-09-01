import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;
let isEditing = false;
let editingNumero = null;
window.App = $App;

const setModalMode = (editing) => {
    isEditing = editing;
    if (!editing) {
        editingNumero = null;
    }
    $('#captureModal .card-header h5').text(editing ? 'Editar destacada' : 'Carga de archivo');
    $('#captureModal').find("[data-toggle='guardar']").text(editing ? 'Actualizar' : 'Guardar');
    $('#preview_actual_wrap').toggle(editing);
    $('#archivo_label').text(editing ? 'Reemplazar imagen' : 'Imagen *');
    $('#archivo_help').text(
        editing
            ? 'Opcional. Formatos permitidos: JPG, JPEG y PNG.'
            : 'Formatos permitidos: JPG, JPEG y PNG.',
    );
    if (validator) {
        validator.destroy();
    }
    validatorInit();
};

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            archivo: { required: () => !isEditing },
        },
        messages: {
            archivo: 'El archivo es requerido',
        },
    });
};

const resetForm = () => {
    const $form = $('#form');
    $form[0].reset();
    $('#archivo').next('.custom-file-label').text('Seleccione un archivo');
    $('#preview_actual').attr('src', '');
    $form.find(':input').prop('disabled', false);
    if (validator) {
        validator.resetForm();
    }
};

const fillForm = ({ archivo_preview = '' } = {}) => {
    resetForm();
    if (archivo_preview) {
        $('#preview_actual').attr('src', archivo_preview);
    }
};

const submitForm = () => {
    if (!validator.valid()) return;

    const formData = new FormData($('#form')[0]);
    if (isEditing && editingNumero) {
        formData.append('numero', editingNumero);
    }

    const $guardarBtn = $('#captureModal').find("[data-toggle='guardar']");
    $guardarBtn.prop('disabled', true);

    window.App.trigger('upload', {
        url: window.App.url(window.ServerController + '/guardar'),
        data: formData,
        callback: (response) => {
            $guardarBtn.prop('disabled', false);

            if (response?.flag === true) {
                Messages.display(response.msg, 'success');
                resetForm();
                editingNumero = null;
                isEditing = false;
                bootstrap.Modal.getInstance(document.getElementById('captureModal'))?.hide();
                galeria();
            } else {
                Messages.display(response?.msg || 'Error al guardar', 'error');
            }
        },
    });
};

const galeria = () => {
    window.App.trigger('syncro', {
        url: window.App.url(window.ServerController + '/galeria'),
        callback: (response) => {
            if (!response || response.flag !== true) {
                return Messages.display(response?.msg || 'No se pudieron cargar los datos', 'error');
            }

            const items = response.data || [];
            if (!items.length) {
                $('#galeria').html(
                    '<div class="galeria-admin-empty">No hay imágenes destacadas. Use "Agregar imagen" para comenzar.</div>',
                );
                return;
            }

            let html = '';
            const tmp = _.template(document.getElementById('tmp_galeria').innerHTML);
            $.each(items, function (key, value) {
                html += tmp({ value });
            });
            $('#galeria').html(html);
        },
        error: (xhr) => {
            Messages.display('Error al cargar las destacadas: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        },
    });
};

$(() => {
    window.App.initialize();
    validatorInit();

    const modalCaptureEl = document.getElementById('captureModal');
    const modalZoom = new bootstrap.Modal(document.getElementById('zoomModal'));
    const modalCapture = new bootstrap.Modal(modalCaptureEl);

    modalCaptureEl.addEventListener('show.bs.modal', (event) => {
        if (!event.relatedTarget || !$(event.relatedTarget).is("[data-bs-target='#captureModal']")) {
            return;
        }
        setModalMode(false);
        fillForm();
    });

    $(document).on('change', '#archivo', (e) => {
        const file = e.target.files?.[0];
        const label = $(e.target).next('.custom-file-label');
        label.text(file ? file.name : 'Seleccione un archivo');
    });

    galeria();

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const numero = $(e.currentTarget).attr('data-cid');

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { numero },
            callback: (response) => {
                if (!response || !response.numero) {
                    return Messages.display('No fue posible cargar el registro.', 'error');
                }

                editingNumero = response.numero;
                setModalMode(true);
                fillForm({ archivo_preview: response.archivo });
                modalCapture.show();
            },
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const numero = $(e.currentTarget).attr('data-cid');
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
                    data: { numero },
                    callback: (response) => {
                        if (response?.flag === true) {
                            galeria();
                            Messages.display(response.msg, 'success');
                        } else {
                            Messages.display(response?.msg || 'Error al borrar', 'error');
                        }
                    },
                    error: (xhr) => {
                        Messages.display('Error al borrar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                    },
                });
            }
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        submitForm();
    });

    $(document).on('click', "[data-toggle='arriba']", (e) => {
        const numero = $(e.currentTarget).attr('data-cid');
        e.preventDefault();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/arriba'),
            data: { numero },
            callback: (response) => {
                if (response?.flag === true) {
                    Messages.display(response.msg, 'success');
                    galeria();
                } else {
                    Messages.display(response?.msg || 'Error al reordenar', 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al reordenar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            },
        });
    });

    $(document).on('click', "[data-toggle='abajo']", (e) => {
        const numero = $(e.currentTarget).attr('data-cid');
        e.preventDefault();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/abajo'),
            data: { numero },
            callback: (response) => {
                if (response?.flag === true) {
                    Messages.display(response.msg, 'success');
                    galeria();
                } else {
                    Messages.display(response?.msg || 'Error al reordenar', 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al reordenar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            },
        });
    });

    $(document).on('click', "[data-toggle='preview']", (e) => {
        e.preventDefault();
        const $target = $(e.currentTarget);
        const file = $target.attr('data-file');
        const archivoNombre = $target.attr('data-archivo-nombre') || '';
        const imageUrl = $target.attr('data-url') || file || '';
        $('#zoomModalbody').html(`
            <p class="text-muted small mb-1 text-break"><strong>Archivo:</strong> ${archivoNombre}</p>
            <p class="text-muted small mb-2 text-break"><strong>URL:</strong> ${imageUrl}</p>
            <img id="img_zoom" class="img-fluid" src="${file}" alt="${archivoNombre}" />
        `);
        modalZoom.show();
    });
});
