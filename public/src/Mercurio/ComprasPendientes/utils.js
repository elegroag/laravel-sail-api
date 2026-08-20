/**
 * Helpers puros y utilitarios de presentacion del modulo ComprasPendientes.
 *
 * Depende de globales del layout: $ (jQuery) y las banderas
 * window.EPAYCO_CHECKOUT_VERSION / window.EPAYCO_TEST / window.epaycoHandler.
 */
export function escapeHtml(texto) {
    if (!texto) return '';
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

export function formatearValor(valor) {
    var num = parseFloat(valor) || 0;
    return '$' + num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

export function sanitizarTexto(texto) {
    if (!texto) return '';
    return texto
        .replace(/[áàäâ]/g, 'a').replace(/[éèëê]/g, 'e')
        .replace(/[íìïî]/g, 'i').replace(/[óòöô]/g, 'o')
        .replace(/[úùüû]/g, 'u').replace(/[ñ]/g, 'n')
        .replace(/[ÁÀÄÂ]/g, 'A').replace(/[ÉÈËÊ]/g, 'E')
        .replace(/[ÍÌÏÎ]/g, 'I').replace(/[ÓÒÖÔ]/g, 'O')
        .replace(/[ÚÙÜÛ]/g, 'U').replace(/[Ñ]/g, 'N')
        .replace(/[^a-zA-Z0-9\s.\-]/g, '');
}

export function mostrarLoader(id) {
    $('#' + id).show();
}

export function ocultarLoader(id) {
    $('#' + id).hide();
}

export function obtenerEpaycoHandler() {
    return window.epaycoHandler || null;
}

export function esCheckoutV2() {
    return String(window.EPAYCO_CHECKOUT_VERSION) === '2';
}

export function epaycoTestActivo() {
    return window.EPAYCO_TEST === true || String(window.EPAYCO_TEST) === 'true';
}
