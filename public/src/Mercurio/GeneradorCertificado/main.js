import { $App } from '@/App';

const MODAL_ID = 'modal_generic';
const MODAL_DIALOG_ID = 'size_modal_generic';
const MODAL_CONTENT_ID = 'show_modal_generic';
const CERTIFICADO_IFRAME_NAME = 'certificado_modal_iframe';

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
