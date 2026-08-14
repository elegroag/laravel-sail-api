import { $App } from '@/App';
import loading from '@/Componentes/Views/Loading';

const MODAL_ID = 'modal_generic';
const MODAL_DIALOG_ID = 'size_modal_generic';
const MODAL_CONTENT_ID = 'show_modal_generic';
const CERTIFICADO_IFRAME_NAME = 'certificado_modal_iframe';
const SEND_EMAIL_FIELD = 'send_email';

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

const openCertificadoModal = (title) => {
    const $dialog = $(`#${MODAL_DIALOG_ID}`);
    if ($dialog.length) {
        $dialog.removeClass().addClass('modal-dialog modal-xl modal-dialog-scrollable');
    }

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
                name="${CERTIFICADO_IFRAME_NAME}"
                style="width: 100%; height: 80vh; border: 0;"
            ></iframe>
        </div>
    `;

    $(`#${MODAL_CONTENT_ID}`).html(html);
    showModal();
};

const sendCertificadoByEmail = async ($form) => {
    const formEl = $form.get(0);
    if (!formEl) {
        return;
    }

    setSendEmailFlag($form, true);
    const formData = new FormData(formEl);
    const $button = $('#bt_certificado_afiliacion');
    $button.prop('disabled', true);
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
    }
};

const submitFormToModalIframe = () => {
    const $form = $('#form');
    if (!$form.length) {
        return;
    }

    if (!$form.valid()) {
        return;
    }

    // Móvil: enviar certificado por correo y mostrar confirmación
    if (isMobileDevice()) {
        sendCertificadoByEmail($form);
        return;
    }

    // Desktop: preview en modal con iframe
    setSendEmailFlag($form, false);
    const prevTarget = $form.attr('target');
    $form.data('prev-target', prevTarget ?? '');

    openCertificadoModal('Certificado / Oficio');
    $form.attr('target', CERTIFICADO_IFRAME_NAME);

    const formEl = $form.get(0);
    if (formEl) {
        formEl.submit();
    }
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
};

$(() => {
    window.App = $App;
    window.App.initialize();

    const rules = {};
    if ($('#cedtra').length) {
        rules.cedtra = { required: true };
    }
    if ($('#tipo').length) {
        rules.tipo = { required: true };
    }

    $('#form').validate({ rules });

    $(document).on('click', '#bt_certificado_afiliacion', submitFormToModalIframe);
    $(document).off('hidden.bs.modal', `#${MODAL_ID}`, cleanupModal);
    $(document).on('hidden.bs.modal', `#${MODAL_ID}`, cleanupModal);
});
