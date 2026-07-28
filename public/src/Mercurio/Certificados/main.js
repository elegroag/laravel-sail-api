import { $App } from '@/App';

const beneficiarios = () => window.CertificadosBeneficiarios || [];

const resetDropzoneLabel = () => {
    $('#certFileMeta').addClass('d-none');
    $('#certFileName').text('');
    $('#certDropzoneContent').removeClass('d-none');
};

const showSelectedFile = (fileName) => {
    $('#certFileName').text(fileName);
    $('#certFileMeta').removeClass('d-none');
    $('#certDropzoneContent').addClass('d-none');
};

const setDropzoneBusy = (busy) => {
    const zone = $('#certDropzone');
    if (busy) {
        zone.addClass('doc-dropzone--busy');
        $('#certDropzoneContent, #certFileMeta').addClass('d-none');
        $('#certDropzoneLoading').removeClass('d-none');
        $('#btnSalvarCertificado').prop('disabled', true);
    } else {
        zone.removeClass('doc-dropzone--busy');
        $('#certDropzoneLoading').addClass('d-none');
        $('#btnSalvarCertificado').prop('disabled', false);
        if ($('#archivo').get(0)?.files?.length) {
            showSelectedFile($('#archivo').get(0).files[0].name);
        } else {
            resetDropzoneLabel();
        }
    }
};

const isPdfFile = (file) => {
    if (!file) return false;
    const nameOk = /\.pdf$/i.test(file.name || '');
    const typeOk = !file.type || file.type === 'application/pdf';
    return nameOk && typeOk;
};

const assignFileToInput = (file) => {
    const input = document.getElementById('archivo');
    if (!input || !file) return false;

    if (!isPdfFile(file)) {
        Swal.fire({
            icon: 'error',
            text: 'Solo se admiten archivos PDF.',
            confirmButtonText: 'Cerrar',
        });
        input.value = '';
        resetDropzoneLabel();
        return false;
    }

    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    showSelectedFile(file.name);
    return true;
};

const fillCertificados = (codben) => {
    const $codcer = $('#codcer');
    $codcer.empty();

    if (!codben) {
        $codcer.append('<option value="">Seleccione primero un beneficiario</option>');
        $codcer.prop('disabled', true);
        return;
    }

    const ben = beneficiarios().find((item) => String(item.codben) === String(codben));
    const certs = ben?.certificados || {};
    const entries = Object.entries(certs);

    if (entries.length === 0) {
        $codcer.append('<option value="">Sin certificados disponibles</option>');
        $codcer.prop('disabled', true);
        return;
    }

    $codcer.append('<option value="">Seleccione tipo de certificado</option>');
    entries.forEach(([code, label]) => {
        $codcer.append($('<option>', { value: code, text: label }));
    });
    $codcer.prop('disabled', false);
};

const guardarFile = () => {
    const codben = $('#codben').val();
    const codcer = $('#codcer').val();
    const input = document.getElementById('archivo');
    const file = input?.files?.[0];

    if (!codben) {
        Swal.fire({ icon: 'error', text: 'Seleccione el beneficiario', confirmButtonText: 'Salir' });
        return;
    }
    if (!codcer) {
        Swal.fire({ icon: 'error', text: 'Seleccione el tipo de certificado', confirmButtonText: 'Salir' });
        return;
    }
    if (!file) {
        Swal.fire({ icon: 'error', text: 'Adjunte el archivo PDF', confirmButtonText: 'Salir' });
        return;
    }
    if (!isPdfFile(file)) {
        Swal.fire({ icon: 'error', text: 'Solo se admiten archivos PDF.', confirmButtonText: 'Salir' });
        return;
    }

    const ben = beneficiarios().find((item) => String(item.codben) === String(codben));
    const nombre = ben?.nombre || '';
    const nomcer = $('#codcer option:selected').text();

    // El backend espera el input como archivo_{codben}
    input.name = 'archivo_' + codben;

    setDropzoneBusy(true);
    $(input).upload(
        $App.url('guardar', window.ServerController ?? 'certificados'),
        {
            codben,
            nombre,
            codcer,
            nomcer,
        },
        function (response) {
            setDropzoneBusy(false);
            input.name = 'archivo';

            if (response.success == true) {
                Swal.fire({
                    icon: 'success',
                    text: response.msj,
                    confirmButtonText: 'OK',
                });
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                Swal.fire({
                    icon: 'error',
                    text: response.error || response.msj || 'No se pudo cargar el certificado',
                    confirmButtonText: 'Salir',
                });
            }
        },
    );
};

$(() => {
    $App.initialize();

    $(document).on('change', '#codben', (e) => {
        fillCertificados($(e.currentTarget).val());
    });

    $(document).on('change', '#archivo', (e) => {
        const file = e.currentTarget.files?.[0];
        if (!file) {
            resetDropzoneLabel();
            return;
        }
        if (!assignFileToInput(file)) {
            e.currentTarget.value = '';
        }
    });

    $(document).on('click', '#certFileClear', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const input = document.getElementById('archivo');
        if (input) input.value = '';
        resetDropzoneLabel();
    });

    const zone = document.getElementById('certDropzone');
    if (zone) {
        ['dragenter', 'dragover'].forEach((evt) => {
            zone.addEventListener(evt, (event) => {
                event.preventDefault();
                event.stopPropagation();
                zone.classList.add('doc-dropzone--active');
            });
        });

        zone.addEventListener('dragleave', (event) => {
            event.preventDefault();
            event.stopPropagation();
            if (!zone.contains(event.relatedTarget)) {
                zone.classList.remove('doc-dropzone--active');
            }
        });

        zone.addEventListener('drop', (event) => {
            event.preventDefault();
            event.stopPropagation();
            zone.classList.remove('doc-dropzone--active');
            if (zone.classList.contains('doc-dropzone--busy')) return;

            const files = event.dataTransfer?.files;
            if (!files || files.length === 0) return;
            assignFileToInput(files[0]);
        });
    }

    $(document).on('click', '#btnSalvarCertificado', (e) => {
        e.preventDefault();
        guardarFile();
    });

    $(document).on('click', '.certificados-btn-archivar', (e) => {
        e.preventDefault();
        const btn = $(e.currentTarget);
        const id = btn.attr('data-id');
        const nombre = btn.attr('data-nombre') || '';
        const nomcer = btn.attr('data-nomcer') || '';

        if (!id) return;

        Swal.fire({
            icon: 'warning',
            title: 'Eliminar solicitud',
            html: `<p style="font-size:0.95rem;margin:0">¿Desea eliminar el certificado <strong>${nomcer}</strong> de <strong>${nombre}</strong>?</p>
                   <p style="font-size:0.82rem;color:#8898aa;margin:0.5rem 0 0">La solicitud se archivará automáticamente. Podrá volver a cargarlo después.</p>`,
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#f5365c',
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                type: 'POST',
                url: $App.url('borrar', window.ServerController ?? 'certificados'),
                data: { id },
                dataType: 'json',
                beforeSend: (xhr) => {
                    const csrf = document.querySelector("[name='csrf-token']")?.getAttribute('content');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    if (csrf) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                    }
                    btn.prop('disabled', true);
                },
                success: (response) => {
                    if (response?.success) {
                        Swal.fire({
                            icon: 'success',
                            text: response.msj || 'Solicitud eliminada',
                            confirmButtonText: 'OK',
                        });
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            text: response?.error || response?.msj || 'No se pudo eliminar la solicitud',
                            confirmButtonText: 'Salir',
                        });
                    }
                },
                error: (xhr) => {
                    btn.prop('disabled', false);
                    const msg =
                        xhr.responseJSON?.msj ||
                        xhr.responseJSON?.error ||
                        xhr.responseJSON?.message ||
                        'No se pudo eliminar la solicitud';
                    Swal.fire({
                        icon: 'error',
                        text: msg,
                        confirmButtonText: 'Salir',
                    });
                },
            });
        });
    });
});
