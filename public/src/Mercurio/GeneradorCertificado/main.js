import { $App } from '@/App';

const MODAL_ID = 'modal_generic';
const MODAL_DIALOG_ID = 'size_modal_generic';
const MODAL_CONTENT_ID = 'show_modal_generic';
const CERTIFICADO_IFRAME_NAME = 'certificado_modal_iframe';

const FORCE_DOWNLOAD_FIELD = 'force_download';

const isMobileDevice = () => {
    return (
        window.innerWidth <= 768 ||
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    );
};

const setForceDownloadFlag = ($form, enabled) => {
    let $field = $form.find(`input[name="${FORCE_DOWNLOAD_FIELD}"]`);
    if (!$field.length) {
        $field = $(`<input type="hidden" name="${FORCE_DOWNLOAD_FIELD}" />`);
        $form.append($field);
    }
    $field.val(enabled ? '1' : '0');
};

const parseFilenameFromContentDisposition = (header, fallback = 'certificado.pdf') => {
    if (!header) {
        return fallback;
    }

    const utfMatch = /filename\*=UTF-8''([^;]+)/i.exec(header);
    if (utfMatch?.[1]) {
        try {
            return decodeURIComponent(utfMatch[1]);
        } catch {
            // ignore decode errors and try the plain filename
        }
    }

    const match = /filename="?([^";]+)"?/i.exec(header);
    return match?.[1]?.trim() || fallback;
};

const showDownloadError = (message) => {
    if (window.App?.trigger) {
        window.App.trigger('alert:error', { message });
        return;
    }

    alert(message);
};

const downloadCertificadoMobile = async ($form) => {
    const formEl = $form.get(0);
    if (!formEl) {
        return;
    }

    setForceDownloadFlag($form, true);
    const formData = new FormData(formEl);

    try {
        const response = await fetch(formEl.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/pdf',
            },
        });

        if (!response.ok) {
            throw new Error(`Error al generar el certificado (${response.status})`);
        }

        const contentType = response.headers.get('Content-Type') || '';
        if (contentType.includes('text/html') || contentType.includes('application/json')) {
            throw new Error('No se pudo generar el certificado. Intenta de nuevo.');
        }

        const blob = await response.blob();
        const filename = parseFilenameFromContentDisposition(response.headers.get('Content-Disposition'));
        const objectUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = objectUrl;
        link.download = filename;
        link.rel = 'noopener';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(() => URL.revokeObjectURL(objectUrl), 1000);
    } catch (error) {
        const message = error instanceof Error ? error.message : 'Error al descargar el certificado';
        showDownloadError(message);
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

const submitFormToModalIframe = () => {
    const $form = $('#form');
    if (!$form.length) {
        return;
    }

    if (!$form.valid()) {
        return;
    }

    // Móvil: descarga forzada vía fetch + blob (sin pestaña vacía)
    if (isMobileDevice()) {
        downloadCertificadoMobile($form);
        return;
    }

    // Desktop: preview en modal con iframe
    setForceDownloadFlag($form, false);
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
