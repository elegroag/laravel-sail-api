import { $App } from '@/App';

(function ($) {
    var fileUploadCount = 0;
    var prepareFiles = [];

    $.fn.fileUpload = function () {
        return this.each(function () {
            const fileUploadDiv = $(this);
            const fileUploadId = `fileUpload-${++fileUploadCount}`;

            // Creates HTML content for the file upload area.
            const fileDivContent = `
                <label for="${fileUploadId}" class="file-upload">
                    <div>
                        <span class="firma-upload-icon"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></span>
                        <b>Validar documento</b>
                        <p>Arrastra y suelta el archivo aquí</p>
                        <span>o</span>
                        <div>Click para buscar archivos</div>
                    </div>
                    <input type="file" id="${fileUploadId}" name=[] multiple hidden />
                </label>
            `;

            fileUploadDiv.html(fileDivContent).addClass('file-container');

            let table = null;
            let tableBody = null;
            // Creates a table containing file information.
            function createTable() {
                table = $(`
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre archivo</th>
                                <th>Tamaño</th>
                                <th>Tipo</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                `);

                tableBody = table.find('tbody');
                fileUploadDiv.append($('<div class="firma-table-wrap"></div>').append(table));
            }

            // Adds the information of uploaded files to table.
            function handleFiles(files) {
                if (!table) createTable();
                tableBody.empty();

                if (files.length > 0) {
                    $.each(files, function (index, file) {
                        if (typeof index == 'number') {
                            prepareFiles[index] = file;
                            let fileName = file.name;
                            let fileSize = (file.size / 1024).toFixed(2) + ' KB';
                            let fileType = file.type;
                            tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${fileName}</td>
                                <td>${fileSize}</td>
                                <td>${fileType}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary validaBtn" data-cid='${index}'>
                                        <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>Validar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn">
                                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        }
                    });

                    tableBody.find('.deleteBtn').click(function () {
                        $(this).closest('tr').remove();
                        if (tableBody.find('tr').length === 0) {
                            tableBody.append('<tr><td colspan="5" class="no-file">No hay archivos seleccionados</td></tr>');
                        }
                    });

                    tableBody.find('.validaBtn').click(function () {
                        const cid = $(this).attr('data-cid');
                        const formData = new FormData();
                        formData.append('file', prepareFiles[cid]);

                        $App.trigger('upload', {
                            url: $App.url('firmas/valida_firma'),
                            data: formData,
                            callback: (response) => {
                                if (response.success == true) {
                                    if (response.hasFirma == false) {
                                        $App.trigger('alert:warning', { message: response.msj });
                                    } else if (response.isValid == true) {
                                        $App.trigger('alert:success', { message: response.msj });
                                    } else {
                                        $App.trigger('alert:warning', { message: response.msj });
                                    }
                                } else {
                                    $App.trigger('alert:error', { message: response.msj });
                                }
                            },
                        });
                    });
                }
            }

            // Events triggered after dragging files.
            fileUploadDiv.on({
                dragover: function (e) {
                    e.preventDefault();
                    fileUploadDiv.toggleClass('dragover', e.type === 'dragover');
                },
                drop: function (e) {
                    e.preventDefault();
                    fileUploadDiv.removeClass('dragover');
                    handleFiles(e.originalEvent.dataTransfer.files);
                },
            });

            // Event triggered when file is selected.
            fileUploadDiv.find(`#${fileUploadId}`).change(function () {
                handleFiles(this.files);
            });
        });
    };
})(jQuery);

const escapeHtml = (value) =>
    String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

// La clave solo se muestra mientras se mantiene pulsado el botón; no se escribe en el DOM hasta entonces
const renderRecoveredSignature = (privateKey, password) => {
    const clave = String(password ?? '');
    const mask = '•'.repeat(clave.length || 6);

    $('#signatureResult').html(`
        <div class="alert alert-success pt-3 pb-3">
            <h6 class="text-white">Firma digital privada:</h6>
            <pre>${escapeHtml(privateKey)}</pre>
            <div class="firma-clave mt-3">
                <h6 class="text-white mb-2">Clave de seguridad para firmar documentos:</h6>
                <div class="firma-clave-row">
                    <code class="firma-clave-valor" data-role="clave-valor">${mask}</code>
                    <button type="button" class="btn btn-sm btn-light firma-clave-reveal" data-role="clave-reveal"
                        aria-label="Mantener pulsado para ver la clave">
                        <i class="fas fa-eye mr-1" aria-hidden="true"></i>Mantener pulsado para ver
                    </button>
                </div>
            </div>
            <p class="mt-3 mb-0">
                La clave también le fue enviada a su correo electrónico. Guarde esta información en un lugar seguro.
            </p>
        </div>
    `);

    const $valor = $('#signatureResult [data-role="clave-valor"]');
    const show = (e) => {
        e.preventDefault();
        $valor.text(clave);
    };
    const hide = () => $valor.text(mask);

    $('#signatureResult [data-role="clave-reveal"]')
        .on('mousedown touchstart', show)
        .on('mouseup mouseleave touchend touchcancel blur', hide)
        .on('keydown', (e) => {
            if (e.key === ' ' || e.key === 'Enter') show(e);
        })
        .on('keyup', hide)
        .on('contextmenu', (e) => e.preventDefault());
};

$(() => {
    $App.initialize();

    $('#fileUpload').fileUpload();

    // Toggle mostrar/ocultar formulario de recuperación
    $('#toggleRecoveryForm').click(function () {
        const $card = $('#recoveryFormCard');
        const $btn = $(this);

        if ($card.is(':visible')) {
            $card.slideUp();
            $btn.html('<i class="fas fa-eye mr-1" aria-hidden="true"></i>Mostrar recuperación de firma');
        } else {
            $card.slideDown();
            $btn.html('<i class="fas fa-eye-slash mr-1" aria-hidden="true"></i>Ocultar recuperación de firma');
        }
    });

    // Manejar recuperación de firma digital
    $('#recoverSignatureBtn').click(function () {
        const systemKey = $('#systemKey').val().trim();

        if (!systemKey) {
            $App.trigger('alert:warning', { message: 'Por favor ingrese su clave del sistema' });
            return;
        }

        $App.trigger('syncro', {
            url: $App.url('firmas/recuperar_firma'),
            data: { systemKey: systemKey },
            callback: (response) => {
                if (response.success == true) {
                    renderRecoveredSignature(response.private_key, response.password);
                    $App.trigger('alert:success', { message: response.msj });
                } else {
                    $('#signatureResult').html(`
                        <div class="alert alert-danger pt-3 pb-3">
                            ${response.msj}
                        </div>
                    `);
                    $App.trigger('alert:error', { message: response.msj });
                }
            },
        });
    });
});
