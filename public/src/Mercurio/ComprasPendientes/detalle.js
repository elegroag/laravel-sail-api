/**
 * Modal de detalle de compra / precompra: servicio, ítems y total.
 *
 * Depende de globales del layout: $ (jQuery), bootstrap.
 */
import store from './store.js';
import { escapeHtml, formatearValor } from './utils.js';
import { nombreServicio } from './datos.js';

/**
 * Normaliza items de una precompra (legacy = solo codben).
 *
 * @returns {Array<{codben:string,nombre:string,valser:number|null}>}
 */
export function itemsDesdePrecompra(precompra) {
    var raw = precompra && Array.isArray(precompra.items) ? precompra.items : null;
    if (raw && raw.length) {
        return raw.map(function (item) {
            var codben = String(isObject(item) ? (item.codben || '') : item);
            var nombre = isObject(item) ? String(item.nombre || '') : '';
            var valser = null;
            if (isObject(item) && item.valser !== undefined && item.valser !== null && item.valser !== '') {
                valser = parseFloat(item.valser);
                if (isNaN(valser)) {
                    valser = null;
                }
            }
            return { codben: codben, nombre: nombre, valser: valser };
        }).filter(function (item) {
            return item.codben !== '';
        });
    }

    var codben = precompra && precompra.codben ? String(precompra.codben) : '';
    if (!codben) {
        return [];
    }

    return [{
        codben: codben,
        nombre: '',
        valser: precompra.valor !== null && precompra.valor !== '' ? parseFloat(precompra.valor) || null : null,
    }];
}

function isObject(value) {
    return value !== null && typeof value === 'object';
}

function buildFila(label, valorHtml) {
    return '<div class="compra-detalle__row">' +
        '<span class="compra-detalle__label">' + escapeHtml(label) + '</span>' +
        '<span class="compra-detalle__value">' + valorHtml + '</span>' +
        '</div>';
}

export function abrirDetallePrecompra(precompra) {
    var items = itemsDesdePrecompra(precompra);
    var total = parseFloat(precompra.valor) || 0;
    var sumaItems = items.reduce(function (acc, item) {
        return acc + (item.valser !== null ? item.valser : 0);
    }, 0);
    if (sumaItems > 0) {
        total = sumaItems;
    }

    var servicio = nombreServicio(precompra);
    var html = '';
    html += '<div class="compra-detalle">';
    html += '<h6 class="compra-detalle__section">Servicio</h6>';
    html += buildFila('Nombre', escapeHtml(servicio));
    html += buildFila('Código', escapeHtml(String(precompra.codser || '-')));
    html += buildFila('Número / apertura', escapeHtml(String(precompra.numero || '-')));
    html += buildFila('Fecha precompra', escapeHtml(precompra.fecha_precompra || '-'));
    if (precompra.ref_payco) {
        html += buildFila('Ref. ePayco', escapeHtml(String(precompra.ref_payco)));
    }
    html += buildFila('Estado', escapeHtml(precompra.estado_descripcion || precompra.estado || '-'));

    html += '<h6 class="compra-detalle__section mt-3">Beneficiarios</h6>';
    if (!items.length) {
        html += '<p class="text-muted mb-0">Sin beneficiarios registrados.</p>';
    } else {
        html += '<ul class="compra-detalle__items list-unstyled mb-0">';
        items.forEach(function (item) {
            html += '<li class="compra-detalle__item">';
            html += '<div class="compra-detalle__item-info">';
            html += '<span class="compra-detalle__item-nombre">' + escapeHtml(item.nombre || item.codben) + '</span>';
            html += '<span class="compra-detalle__item-doc">' + escapeHtml(item.codben) + '</span>';
            html += '</div>';
            html += '<span class="compra-detalle__item-valor">' +
                (item.valser !== null ? formatearValor(item.valser) : '—') +
                '</span>';
            html += '</li>';
        });
        html += '</ul>';
    }

    html += '<div class="compra-detalle__total mt-3">';
    html += '<span>Total a pagar</span>';
    html += '<strong>' + formatearValor(total) + '</strong>';
    html += '</div>';
    html += '</div>';

    $('#modal_detalle_compra_titulo').text('Detalle de compra #' + (precompra.id || ''));
    $('#modal_detalle_compra_body').html(html);

    if (!store.modalDetalleCompra) {
        var el = document.getElementById('modal_detalle_compra');
        if (el && typeof bootstrap !== 'undefined') {
            store.modalDetalleCompra = new bootstrap.Modal(el);
        }
    }
    if (store.modalDetalleCompra) {
        store.modalDetalleCompra.show();
    }
}
