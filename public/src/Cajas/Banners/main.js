import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;
let isEditing = false;
let editingId = null;
window.App = $App;

const setModalMode = (editing) => {
    isEditing = editing;
    if (!editing) {
        editingId = null;
    }
    $('#captureModal .card-header h5').text(editing ? 'Editar banner' : 'Nuevo banner');
    $('#captureModal').find("[data-toggle='guardar']").text(editing ? 'Actualizar' : 'Guardar');
    $('#preview_actual_wrap').toggle(editing);
    $('#imagen_label').text(editing ? 'Reemplazar imagen' : 'Imagen');
    $('#imagen_help').text(
        editing
            ? 'Opcional si ya hay imagen o URL. Formatos: JPG, JPEG y PNG.'
            : 'Requerido si no indica URL. Formatos: JPG, JPEG y PNG.',
    );
    if (validator) {
        validator.destroy();
    }
    validatorInit();
};

const hasImageSource = () => {
    const hasFile = $('#imagen')[0]?.files?.length > 0;
    const hasUrl = String($('#url_imagen').val() || '').trim() !== '';
    const hasPreview = isEditing && Boolean($('#preview_actual').attr('src'));
    return hasFile || hasUrl || hasPreview;
};

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            estado: { required: true },
            fecha_inicia: { required: true },
            fecha_finaliza: { required: true },
        },
        messages: {
            estado: 'El estado es requerido',
            fecha_inicia: 'La fecha de inicio es requerida',
            fecha_finaliza: 'La fecha de finalización es requerida',
        },
    });
};

const resetForm = () => {
    const $form = $('#form');
    $form[0].reset();
    $('#estado').val('A');
    $('#imagen').next('.custom-file-label').text('Seleccione un archivo');
    $('#preview_actual').attr('src', '');
    $('#content_html').val('');
    $('#url_imagen').val('');
    $form.find(':input').prop('disabled', false);
    if (validator) {
        validator.resetForm();
    }
};

const fillForm = ({
    imagen_preview = '',
    estado = 'A',
    fecha_inicia = '',
    fecha_finaliza = '',
    url_imagen = '',
    content_html = '',
} = {}) => {
    resetForm();
    $('#estado').val(estado || 'A');
    $('#fecha_inicia').val(fecha_inicia || '');
    $('#fecha_finaliza').val(fecha_finaliza || '');
    $('#url_imagen').val(url_imagen || '');
    $('#content_html').val(content_html || '');
    if (imagen_preview) {
        $('#preview_actual').attr('src', imagen_preview);
    }
};

const submitForm = () => {
    if (!validator.valid()) return;

    if (!hasImageSource()) {
        Messages.display('Debe cargar una imagen o indicar una URL de imagen.', 'error');
        return;
    }

    const formData = new FormData($('#form')[0]);
    if (isEditing && editingId) {
        formData.append('id', editingId);
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
                editingId = null;
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
                    '<div class="galeria-admin-empty">No hay banners. Use "Agregar banner" para comenzar.</div>',
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
            Messages.display('Error al cargar los banners: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
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

    $(document).on('change', '#imagen', (e) => {
        const file = e.target.files?.[0];
        const label = $(e.target).next('.custom-file-label');
        label.text(file ? file.name : 'Seleccione un archivo');
    });

    galeria();

    $(document).on('click', "[data-toggle='editar']", (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).attr('data-cid');

        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
            data: { id },
            callback: (response) => {
                if (!response || !response.id) {
                    return Messages.display('No fue posible cargar el registro.', 'error');
                }

                editingId = response.id;
                setModalMode(true);
                fillForm({
                    imagen_preview: response.imagen,
                    estado: response.estado,
                    fecha_inicia: response.fecha_inicia,
                    fecha_finaliza: response.fecha_finaliza,
                    url_imagen: response.url_imagen || '',
                    content_html: response.content_html || '',
                });
                modalCapture.show();
            },
        });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).attr('data-cid');
        Swal.fire({
            title: '¿Está seguro de borrar?',
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
                    data: { id },
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

    $(document).on('click', "[data-toggle='preview']", (e) => {
        e.preventDefault();
        const $target = $(e.currentTarget);
        const file = $target.attr('data-file');
        const archivoNombre = $target.attr('data-archivo-nombre') || '';
        const imageUrl = $target.attr('data-url') || file || '';
        const estado = $target.attr('data-estado') || '';
        const fechas = $target.attr('data-fechas') || '';
        $('#zoomModalbody').html(`
            <p class="text-muted small mb-1 text-break"><strong>Archivo:</strong> ${archivoNombre}</p>
            <p class="text-muted small mb-1 text-break"><strong>URL:</strong> ${imageUrl}</p>
            <p class="text-muted small mb-1 text-break"><strong>Estado:</strong> ${estado}</p>
            <p class="text-muted small mb-2 text-break"><strong>Vigencia:</strong> ${fechas}</p>
            <img id="img_zoom" class="img-fluid" src="${file}" alt="${archivoNombre}" />
        `);
        modalZoom.show();
    });
});
