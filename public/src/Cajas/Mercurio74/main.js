import { $App } from '@/App';
import { Messages } from '@/Utils';

window.App = $App;
let validator;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            archivo: { required: true },
            url: { required: true },
        },
    });
};

const resetForm = () => {
    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
    $('#captureModalbody').html(tpl({}));
    $('#archivo').next('.custom-file-label').text('Seleccione un archivo');
    $('#form :input').prop('disabled', false);
};

$(() => {
    window.App.initialize();
    validatorInit();
    const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
    const modalImagen = new bootstrap.Modal(document.getElementById('modalImagen'));
    const $guardarBtn = $("[data-toggle='guardar']");

    const galeria = () => {
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/galeria'),
            callback: (response) => {
                if (!response || response.flag !== true) {
                    $('#galeria').html('');
                    return Messages.display(response?.msg || 'No se pudieron cargar los datos', 'error');
                }

                let html = '';
                const tpl = _.template(document.getElementById('tmp_galeria_item').innerHTML);
                $.each(response.data || [], (key, value) => {
                    html += tpl({ value });
                });
                $('#galeria').html(html);
            },
        });
    };

    $(document).on({
        mouseenter: function () {
            $(this)
                .css({ outline: '0px solid #6EE0FF' })
                .stop()
                .animate({ outlineWidth: '2px', outlineColor: '#6EE0FF' }, 200);
        },
        mouseleave: function () {
            $(this).stop().animate({ outlineWidth: '0px', outlineColor: '#037736' }, 150);
        },
    }, '.thumbnail');

    $(document).on('change', '#archivo', (e) => {
        const file = e.target.files?.[0];
        const label = $(e.target).next('.custom-file-label');
        label.text(file ? file.name : 'Seleccione un archivo');
    });

    galeria();

    $(document).on('click', "[data-toggle='show-modal']", (e) => {
        e.preventDefault();
        const file = $(e.currentTarget).attr('data-file');
        $('#modalImagenbody').html(`<img id="img_zoom" class="img-fluid" src="${file}" alt="Promoción" />`);
        modalImagen.show();
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        e.stopPropagation();
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
                });
            }
        });
    });

    $(document).on('click', "[data-toggle='arriba']", (e) => {
        e.preventDefault();
        e.stopPropagation();
        const numpro = $(e.currentTarget).attr('data-cid');

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
        });
    });

    $(document).on('click', "[data-toggle='abajo']", (e) => {
        e.preventDefault();
        e.stopPropagation();
        const numpro = $(e.currentTarget).attr('data-cid');

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
        });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();
        if (!validator || !validator.valid()) {
            return;
        }

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
                    modalCapture.hide();
                    galeria();
                } else {
                    Messages.display(response?.msg || 'Error al guardar', 'error');
                }
            },
        });
    });

    $(document).on('click', "[data-toggle='header-nuevo']", (e) => {
        e.preventDefault();
        resetForm();
        modalCapture.show();
        validatorInit();
    });
});
