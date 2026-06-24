import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;
window.App = $App;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            archivo: { required: true },
            tipo: { required: true },
        },
        messages: {
            archivo: 'El archivo es requerido',
            tipo: 'El tipo es requerido',
        },
    });
};

const resetForm = () => {
    $('#form')[0].reset();
    $('#archivo').next('.custom-file-label').text('Seleccione un archivo');
    $('#form :input').prop('disabled', false);
};

const detectTipoFromFile = (fileName) => {
    const extension = fileName.split('.').pop()?.toLowerCase();
    if (extension === 'mp4') {
        return 'V';
    }
    if (['jpg', 'jpeg', 'png'].includes(extension)) {
        return 'F';
    }
    return '';
};

$(() => {
    window.App.initialize();
    validatorInit();
    const modalZoom = new bootstrap.Modal(document.getElementById('zoomModal'));
    const $guardarBtn = $("[data-toggle='guardar']");

    const galeria = () => {
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/galeria'),
            callback: (response) => {
                if (!response || response.flag !== true) {
                    return Messages.display(response?.msg || 'No se pudieron cargar los datos', 'error');
                }

                let html = '';
                const tmp = _.template(document.getElementById('tmp_galeria').innerHTML);
                $.each(response.data || [], function (key, value) {
                    html += tmp({ value });
                });
                $('#galeria').html(html);
            },
            error: (xhr) => {
                Messages.display('Error al cargar la galería: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            },
        });
    };

    $(document).on({
        mouseenter: function () {
            $(this)
                .css({
                    outline: '0px solid #6EE0FF',
                })
                .stop()
                .animate(
                    {
                        outlineWidth: '2px',
                        outlineColor: '#6EE0FF',
                    },
                    200,
                );
        },
        mouseleave: function () {
            $(this).stop().animate(
                {
                    outlineWidth: '0px',
                    outlineColor: '#037736',
                },
                150,
            );
        },
    },
    '.thumbnail',
    );

    $(document).on('change', '#archivo', (e) => {
        const file = e.target.files?.[0];
        const label = $(e.target).next('.custom-file-label');

        if (!file) {
            label.text('Seleccione un archivo');
            return;
        }

        label.text(file.name);

        const tipo = detectTipoFromFile(file.name);
        if (tipo) {
            $('#tipo').val(tipo);
        }
    });

    galeria();

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
        if (!validator.valid()) return;

        const formData = new FormData($('#form')[0]);
        $guardarBtn.prop('disabled', true);

        window.App.trigger('upload', {
            url: window.App.url(window.ServerController + '/guardar'),
            data: formData,
            callback: (response) => {
                $guardarBtn.prop('disabled', false);

                if (response?.flag === true) {
                    Messages.display(response.msg, 'success');
                    resetForm();
                    galeria();
                } else {
                    Messages.display(response?.msg || 'Error al guardar', 'error');
                }
            },
        });
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

    $(document).on('click', "[data-toggle='show-modal']", (e) => {
        e.preventDefault();
        const file = $(e.currentTarget).attr('data-file');
        const isVideo = file?.toLowerCase().endsWith('.mp4');
        if (isVideo) {
            $('#zoomModalbody').html(`<video class="w-100" controls><source src="${file}" type="video/mp4"></video>`);
        } else {
            $('#zoomModalbody').html(`<img id="img_zoom" class="img-fluid" src="${file}" />`);
        }
        modalZoom.show();
    });
});
