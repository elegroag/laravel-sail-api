import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;
let isEditing = false;
let editingNumpro = null;
window.App = $App;

const setModalMode = (editing) => {
    isEditing = editing;
    if (!editing) {
        editingNumpro = null;
    }
    $('#captureModal .card-header h5').text(editing ? 'Editar promoción' : 'Carga de archivo');
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
            estado: { required: true },
            archivo: { required: () => !isEditing },
        },
        messages: {
            estado: 'El estado es requerido',
            archivo: 'El archivo es requerido',
        },
    });
};

const resetForm = () => {
    const $form = $('#form');
    $form[0].reset();
    $('#estado').val('A');
    $('#archivo').next('.custom-file-label').text('Seleccione un archivo');
    $('#preview_actual').attr('src', '');
    $form.find(':input').prop('disabled', false);
    if (validator) {
        validator.resetForm();
    }
};

const fillForm = ({ archivo_preview = '', estado = 'A' } = {}) => {
    resetForm();
    $('#estado').val(estado || 'A');
    if (archivo_preview) {
        $('#preview_actual').attr('src', archivo_preview);
    }
};

const submitForm = () => {
    if (!validator.valid()) return;

    const formData = new FormData($('#form')[0]);
    if (isEditing && editingNumpro) {
        formData.append('numpro', editingNumpro);
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
                editingNumpro = null;
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
                    '<div class="galeria-admin-empty">No hay promociones. Use "Agregar promoción" para comenzar.</div>',
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
            Messages.display('Error al cargar las promociones: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
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
        const numpro = $(e.currentTarget).attr('data-cid');

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { numpro },
            callback: (response) => {
                if (!response || !response.numpro) {
                    return Messages.display('No fue posible cargar el registro.', 'error');
                }

                editingNumpro = response.numpro;
                setModalMode(true);
                fillForm({
                    archivo_preview: response.archivo,
                    estado: response.estado,
                });
                modalCapture.show();
            },
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const numpro = $(e.currentTarget).attr('data-cid');
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
                    data: { numpro },
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
        const numpro = $(e.currentTarget).attr('data-cid');
        e.preventDefault();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/arriba'),
            data: { numpro },
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
        const numpro = $(e.currentTarget).attr('data-cid');
        e.preventDefault();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/abajo'),
            data: { numpro },
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
        const estado = $target.attr('data-estado') || '';
        $('#zoomModalbody').html(`
            <p class="text-muted small mb-1 text-break"><strong>Archivo:</strong> ${archivoNombre}</p>
            <p class="text-muted small mb-1 text-break"><strong>URL:</strong> ${imageUrl}</p>
            <p class="text-muted small mb-2 text-break"><strong>Estado:</strong> ${estado}</p>
            <img id="img_zoom" class="img-fluid" src="${file}" alt="${archivoNombre}" />
        `);
        modalZoom.show();
    });
});
