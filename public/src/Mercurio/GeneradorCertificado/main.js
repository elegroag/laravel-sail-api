import { $App } from '@/App';
import loading from '@/Componentes/Views/Loading';

const MODAL_ID = 'modal_generic';
const MODAL_DIALOG_ID = 'size_modal_generic';
const MODAL_CONTENT_ID = 'show_modal_generic';
const CERTIFICADO_IFRAME_NAME = 'certificado_modal_iframe';
const SEND_EMAIL_FIELD = 'send_email';
const TIPO_FIELD = 'tipo';
const CERTIFICADO_CARD_SELECTOR = '[data-certificado-tipo]';

let certificadoObjectUrl = null;

const isMobileDevice = () => {
    return (
        window.innerWidth <= 768 ||
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    );
};

const setSendEmailFlag = ($form, enabled) => {
    let $field = $form.find(`input[name="${SEND_EMAIL_FIELD}"]`);
    if (!$field.length) {
        $field = $(`<input type="hidden" name="${SEND_EMAIL_FIELD}" />`);
        $form.append($field);
    }
    $field.val(enabled ? '1' : '0');
};

const setTipoCertificado = (tipo) => {
    const $field = $(`#${TIPO_FIELD}`);
    if ($field.length) {
        $field.val(tipo);
    }
};

const setCertificadoLoading = ($card, isLoading) => {
    if (!$card || !$card.length) {
        return;
    }
    $card.toggleClass('is-loading', isLoading);
    $card.toggleClass('is-disabled', isLoading);
};

const clearCertificadoLoading = () => {
    $(CERTIFICADO_CARD_SELECTOR).removeClass('is-loading is-disabled');
};

const revokeCertificadoObjectUrl = () => {
    if (certificadoObjectUrl) {
        URL.revokeObjectURL(certificadoObjectUrl);
        certificadoObjectUrl = null;
    }
};

const showModal = () => {
    const el = document.getElementById(MODAL_ID);
    if (!el) {
        return;
    }

    if (window.bootstrap?.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(el).show();
        return;
    }

    if (typeof window.$ !== 'undefined' && typeof window.$(el).modal === 'function') {
        window.$(el).modal('show');
    }
};

const showError = (message) => {
    if (window.App?.trigger) {
        window.App.trigger('alert:error', { message });
        return;
    }

    alert(message);
};

const openEmailSentModal = (emailMasked) => {
    const $dialog = $(`#${MODAL_DIALOG_ID}`);
    if ($dialog.length) {
        $dialog.removeClass().addClass('modal-dialog modal-dialog-centered');
    }

    const emailText = emailMasked
        ? `al correo <strong>${emailMasked}</strong>`
        : 'al correo registrado en su cuenta';

    const html = `
        <div class="card-header py-3 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Certificado enviado</h5>
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal" data-dismiss="modal"></button>
            </div>
        </div>
        <div class="card-body pt-0 pb-4 px-4">
            <div class="text-center mb-3">
                <i class="fas fa-shield-alt fa-2x text-success" aria-hidden="true"></i>
            </div>
            <p class="mb-2 text-center">
                Por seguridad, el certificado se ha enviado ${emailText}.
            </p>
            <p class="mb-0 text-center text-muted small">
                Revise su bandeja de entrada o carpeta de spam.
            </p>
            <div class="d-grid mt-4">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" data-dismiss="modal">
                    Entendido
                </button>
            </div>
        </div>
    `;

    $(`#${MODAL_CONTENT_ID}`).html(html);
    showModal();
};

const openCertificadoModal = (title, pdfUrl = null, fileName = null) => {
    const $dialog = $(`#${MODAL_DIALOG_ID}`);
    if ($dialog.length) {
        $dialog.removeClass().addClass('modal-dialog modal-xl modal-dialog-scrollable');
    }

    const iframeSrc = pdfUrl ? ` src="${pdfUrl}"` : '';
    const footer = pdfUrl
        ? `
        <div class="card-footer py-2 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-dismiss="modal">
                Cerrar
            </button>
            <a href="${pdfUrl}" download="${fileName || 'certificado.pdf'}" class="btn btn-primary" id="bt_descargar_certificado">
                <i class="fas fa-download me-1" aria-hidden="true"></i>
                Descargar certificado
            </a>
        </div>`
        : '';

    const html = `
        <div class="card-header py-2">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">${title}</h5>
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal" data-dismiss="modal"></button>
            </div>
        </div>
        <div class="card-body p-0">
            <iframe
                id="${CERTIFICADO_IFRAME_NAME}"
                name="${CERTIFICADO_IFRAME_NAME}"${iframeSrc}
                style="width: 100%; height: 80vh; border: 0;"
            ></iframe>
        </div>
        ${footer}
    `;

    $(`#${MODAL_CONTENT_ID}`).html(html);
    showModal();
};

const sendCertificadoByEmail = async ($form, $card = null) => {
    const formEl = $form.get(0);
    if (!formEl) {
        return;
    }

    setSendEmailFlag($form, true);
    const formData = new FormData(formEl);
    const $button = $('#bt_certificado_afiliacion');
    $button.prop('disabled', true);
    setCertificadoLoading($card, true);
    loading.show();

    try {
        const response = await fetch(formEl.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        });

        let payload = null;
        try {
            payload = await response.json();
        } catch {
            payload = null;
        }

        if (!response.ok || !payload?.success) {
            throw new Error(payload?.msj || `No se pudo enviar el certificado (${response.status})`);
        }

        openEmailSentModal(payload.email_masked);
    } catch (error) {
        const message = error instanceof Error ? error.message : 'Error al enviar el certificado por correo';
        showError(message);
    } finally {
        loading.hide();
        $button.prop('disabled', false);
        setCertificadoLoading($card, false);
    }
};

/**
 * Procesa el certificado y lo deja disponible en el modal (vista previa + descarga)
 * usando el mismo archivo generado, sin volver a generarlo.
 */
const processCertificadoDesktop = async ($form, $card = null, titulo = null) => {
    const formEl = $form.get(0);
    if (!formEl) {
        return;
    }

    setSendEmailFlag($form, false);
    const formData = new FormData(formEl);
    setCertificadoLoading($card, true);
    loading.show();

    try {
        const response = await fetch(formEl.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            let payload = null;
            try {
                payload = await response.json();
            } catch {
                payload = null;
            }
            throw new Error(payload?.msj || `No se pudo generar el certificado (${response.status})`);
        }

        const contentType = response.headers.get('Content-Type') || '';
        if (!contentType.includes('application/pdf')) {
            throw new Error('La sesión expiró o no se pudo generar el certificado.');
        }

        const disposition = response.headers.get('Content-Disposition') || '';
        const match = disposition.match(/filename="?([^";]+)"?/);
        const fileName = match ? match[1] : 'certificado.pdf';

        revokeCertificadoObjectUrl();
        const blob = await response.blob();
        certificadoObjectUrl = URL.createObjectURL(blob);

        openCertificadoModal(titulo || 'Certificado', certificadoObjectUrl, fileName);
    } catch (error) {
        const message = error instanceof Error ? error.message : 'Error al generar el certificado';
        showError(message);
    } finally {
        loading.hide();
        setCertificadoLoading($card, false);
    }
};

/**
 * Flujo legacy (vista de empresa): el formulario se envía al iframe del modal.
 */
const submitFormToModalIframe = ($card = null, tipo = null, titulo = null) => {
    const $form = $('#form');
    if (!$form.length) {
        return;
    }

    if (tipo) {
        setTipoCertificado(tipo);
    }

    // El campo tipo es oculto en la vista de recuadros: se valida de forma explícita
    const $campoTipo = $(`#${TIPO_FIELD}`);
    if ($campoTipo.length && !$campoTipo.val()) {
        showError('Seleccione el certificado que desea procesar.');
        return;
    }

    if (!$form.valid()) {
        return;
    }

    // Móvil: enviar certificado por correo y mostrar confirmación
    if (isMobileDevice()) {
        sendCertificadoByEmail($form, $card);
        return;
    }

    // Desktop: preview en modal con iframe
    setSendEmailFlag($form, false);
    const prevTarget = $form.attr('target');
    $form.data('prev-target', prevTarget ?? '');

    setCertificadoLoading($card, true);
    openCertificadoModal(titulo || 'Certificado / Oficio');
    $form.attr('target', CERTIFICADO_IFRAME_NAME);

    const formEl = $form.get(0);
    if (formEl) {
        formEl.submit();
    }
};

const processCertificadoCard = ($card) => {
    const $form = $('#form');
    if (!$form.length) {
        return;
    }

    const tipo = $card.data('certificado-tipo');
    const titulo = $card.data('certificado-titulo');

    if (tipo) {
        setTipoCertificado(tipo);
    }

    const $campoTipo = $(`#${TIPO_FIELD}`);
    if ($campoTipo.length && !$campoTipo.val()) {
        showError('Seleccione el certificado que desea procesar.');
        return;
    }

    if (!$form.valid()) {
        return;
    }

    // Móvil: enviar certificado por correo y mostrar confirmación
    if (isMobileDevice()) {
        sendCertificadoByEmail($form, $card);
        return;
    }

    // Desktop: procesar y mostrar el PDF con opción de descarga
    processCertificadoDesktop($form, $card, titulo);
};

const cleanupModal = () => {
    const $form = $('#form');
    if ($form.length) {
        const prevTarget = $form.data('prev-target');
        if (prevTarget) {
            $form.attr('target', prevTarget);
        } else {
            $form.removeAttr('target');
        }
    }

    const iframeEl = document.getElementById(CERTIFICADO_IFRAME_NAME);
    if (iframeEl) {
        iframeEl.setAttribute('src', 'about:blank');
    }

    revokeCertificadoObjectUrl();
    clearCertificadoLoading();
};

$(() => {
    window.App = $App;
    window.App.initialize();

    const rules = {};
    if ($('#cedtra').length) {
        rules.cedtra = { required: true };
    }

    $('#form').validate({ rules });

    $(document).on('click', '#bt_certificado_afiliacion', () => submitFormToModalIframe());
    $(document).on('click', CERTIFICADO_CARD_SELECTOR, function (event) {
        event.preventDefault();
        processCertificadoCard($(this));
    });
    $(document).on('keydown', CERTIFICADO_CARD_SELECTOR, function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            $(this).trigger('click');
        }
    });
    $(document).off('hidden.bs.modal', `#${MODAL_ID}`, cleanupModal);
    $(document).on('hidden.bs.modal', `#${MODAL_ID}`, cleanupModal);
});
