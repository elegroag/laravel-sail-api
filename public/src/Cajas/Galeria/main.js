import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;
window.App = $App;

const validatorInit = () => {
     validator = $('#form').validate({
        rules: {
            archivo: { required: true },
        },
    });
};

$(() => {
    window.App.initialize();
    const modalZoom = new bootstrap.Modal(document.getElementById('zoomModal'));

    const galeria = () => {
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/galeria'),
            callback: (response) => {
                if(!response) return Messages.display('No se pudieron cargar los datos', 'error');
                let html = '';
                const tmp = _.template(document.getElementById('tmp_galeria').innerHTML);
                $.each(response.data, function (key, value) {
                    html += tmp({ value });
                });
                $('#galeria').html(html);
            },
            error: (xhr) => {
                Messages.display('Error al cargar la galería: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            },
        });
    }

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
                        if (response['flag'] == true) {
                            galeria();
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

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        if (!validator.valid()) return;
        $.ajax({
            type: 'POST',
            url: window.App.url(window.ServerController + '/guardar'),
            data: new FormData($('#form')[0]),
            processData: false,
            contentType: false,
        })
            .done(function (response) {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    $('#form :input').each(function () {
                        $(this).val('');
                        $(this).removeAttr('disabled');
                    });
                    galeria();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            })
            .fail(function (jqXHR) {
                Messages.display('Error al guardar: ' + (jqXHR.responseJSON?.message || jqXHR.statusText), 'error');
            });
    });

    $(document).on('click', "[data-toggle='arriba']", (e) => {
        const numero = $(e.currentTarget).attr('data-cid');
        e.preventDefault();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/arriba'),
            data: { numero },
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    galeria();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al reordenar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    });

    $(document).on('click', "[data-toggle='abajo']", (e) => {
        const numero = $(e.currentTarget).attr('data-cid');
        e.preventDefault();
        window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/abajo'),
            data: { numero },
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    galeria();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al reordenar: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
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
