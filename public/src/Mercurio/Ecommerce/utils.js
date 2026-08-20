/**
 * Helpers puros y utilitarios de presentacion del modulo Ecommerce.
 *
 * Depende de globales del layout: $ (jQuery), Noty.
 */
import { TIPOS_BEN } from './constants.js';

export function formatearValor(valor) {
    var num = parseFloat(valor) || 0;
    return '$' + num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

export function mostrarLoader(id) {
    $('#' + id).show();
}

export function ocultarLoader(id) {
    $('#' + id).hide();
}

export function mostrarNoty(type, message, timeout) {
    if (typeof Noty === 'undefined') {
        return;
    }

    new Noty({
        text: message,
        layout: 'topRight',
        theme: 'relax',
        type: type,
        timeout: timeout || (type === 'error' ? 10000 : 6000),
    }).show();
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

export function escapeHtml(texto) {
    if (!texto) return '';
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

export function obtenerTipoBeneficiario(ben) {
    return ben.descripcion_tipo || TIPOS_BEN[ben.tipben] || ben.tipben || '';
}

export function obtenerCodben(ben) {
    return ben.codben || ben.cedtra || '';
}
