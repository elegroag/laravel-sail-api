/**
 * Helpers puros y utilitarios de presentacion del modulo ComprasPendientes.
 *
 * Depende de globales del layout: $ (jQuery) y las banderas
 * window.EPAYCO_CHECKOUT_VERSION / window.EPAYCO_TEST / window.epaycoHandler.
 */
var CHECKOUT_V2_SRC = 'https://checkout.epayco.co/checkout-v2.js';

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

export function configurarCheckoutV1(publicKey, test) {
    if (!publicKey) {
        return null;
    }

    window.EPAYCO_TEST = !!test;
    try {
        window.epaycoHandler = ePayco.checkout.configure({
            key: publicKey,
            test: !!test,
        });
        return window.epaycoHandler;
    } catch (e) {
        console.log('Error inicializando ePayco v1:', e);
        return null;
    }
}

export function esVistaMovil() {
    return window.matchMedia('(max-width: 991.98px)').matches;
}

export function esEntornoMovil() {
    if (window.isFlutterWebView === true || typeof window.FlutterChannel !== 'undefined') {
        return true;
    }

    var ua = navigator.userAgent || '';
    if (/\bwv\b/i.test(ua)) {
        return true;
    }

    if (/Android|iPhone|iPad|iPod/i.test(ua) && esVistaMovil()) {
        return true;
    }

    return esVistaMovil();
}

/**
 * Desktop: respeta EPAYCO_CHECKOUT_VERSION.
 * Movil/WebView: siempre Smart Checkout v2.
 */
export function esCheckoutV2() {
    return String(window.EPAYCO_CHECKOUT_VERSION) === '2' || esEntornoMovil();
}

export function tipoCheckoutV2() {
    return esEntornoMovil() ? 'standard' : 'onpage';
}

export function epaycoTestActivo(testOverride) {
    if (typeof testOverride === 'boolean') {
        return testOverride;
    }
    return window.EPAYCO_TEST === true || String(window.EPAYCO_TEST) === 'true';
}

export function asegurarSdkCheckoutV2(done) {
    var finish = typeof done === 'function' ? done : function () {};

    if (String(window.EPAYCO_CHECKOUT_VERSION) === '2' || window.__epaycoCheckoutV2Ready) {
        window.__epaycoCheckoutV2Ready = true;
        finish(null);
        return;
    }

    var existing = document.querySelector('script[data-epayco-checkout-v2]');
    if (existing) {
        existing.addEventListener('load', function () {
            window.__epaycoCheckoutV2Ready = true;
            finish(null);
        });
        existing.addEventListener('error', function () {
            finish(new Error('No se pudo cargar ePayco Smart Checkout'));
        });
        return;
    }

    var script = document.createElement('script');
    script.src = CHECKOUT_V2_SRC;
    script.async = true;
    script.setAttribute('data-epayco-checkout-v2', '1');
    script.onload = function () {
        window.__epaycoCheckoutV2Ready = true;
        finish(null);
    };
    script.onerror = function () {
        finish(new Error('No se pudo cargar ePayco Smart Checkout'));
    };
    document.head.appendChild(script);
}
