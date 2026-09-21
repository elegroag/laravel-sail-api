/**
 * Render de las tarjetas de compras pendientes y su listado.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';
import { escapeHtml, formatearValor } from './utils.js';
import { nombreServicio } from './datos.js';
import { itemsDesdePrecompra } from './detalle.js';

function buildFila(label, valor) {
    return '<div class="compra-card__row">' +
        '<span class="compra-card__label">' + escapeHtml(label) + '</span>' +
        '<span class="compra-card__value">' + valor + '</span>' +
        '</div>';
}

function resumenBeneficiarios(precompra) {
    var items = itemsDesdePrecompra(precompra);
    if (!items.length) {
        return escapeHtml(precompra.codben || '-');
    }
    if (items.length === 1) {
        var unico = items[0];
        return escapeHtml(unico.nombre || unico.codben);
    }
    return escapeHtml(String(items.length) + ' beneficiarios');
}

function buildCardPendiente(precompra) {
    var servicio = escapeHtml(nombreServicio(precompra));
    var fecha = escapeHtml(precompra.fecha_precompra || '-');
    var valor = precompra.valor !== null && precompra.valor !== '' ? formatearValor(precompra.valor) : '-';
    var items = itemsDesdePrecompra(precompra);

    var html = '<article class="compra-card" data-precompra-id="' + escapeHtml(String(precompra.id)) + '">';
    html += '<div class="compra-card__header">';
    html += '<span class="compra-card__doc">#' + escapeHtml(String(precompra.id)) + '</span>';
    html += '<span class="compra-card__estado compra-card__estado--x">' + escapeHtml(precompra.estado_descripcion || 'Pendiente de pago') + '</span>';
    html += '</div>';
    html += '<h3 class="compra-card__servicio">' + servicio + '</h3>';
    html += '<div class="compra-card__body">';
    html += buildFila('Fecha', fecha);
    html += buildFila('Beneficiarios', resumenBeneficiarios(precompra));
    if (items.length > 1) {
        html += buildFila('Documentos', escapeHtml(items.map(function (i) { return i.codben; }).join(', ')));
    }

    if (precompra.ref_payco) {
        html += buildFila('Ref. ePayco', escapeHtml(String(precompra.ref_payco)));
    }

    html += '</div>';
    html += '<div class="compra-card__footer">';
    html += '<span class="compra-card__valor-label">Total a pagar</span>';
    html += '<span class="compra-card__valor">' + valor + '</span>';
    html += '</div>';
    html += '<div class="d-flex flex-wrap gap-2 mt-3">';
    html += '<button type="button" class="btn btn-outline-secondary btn-sm flex-fill btn-detalle-compra" data-id="' + escapeHtml(String(precompra.id)) + '">' +
        '<i class="fas fa-list me-1"></i> Ver detalle</button>';
    if (precompra.ref_payco) {
        html += '<button type="button" class="btn btn-success btn-sm flex-fill btn-verificar-pago" data-id="' + escapeHtml(String(precompra.id)) + '">' +
            '<i class="fas fa-sync-alt me-1"></i> Verificar pago</button>';
    }
    html += '<button type="button" class="btn btn-primary btn-sm flex-fill btn-retomar-pago" data-id="' + escapeHtml(String(precompra.id)) + '">' +
        '<i class="fas fa-credit-card me-1"></i> Retomar pago</button>';
    html += '<button type="button" class="btn btn-outline-danger btn-sm flex-fill btn-desestimar" data-id="' + escapeHtml(String(precompra.id)) + '">' +
        '<i class="fas fa-ban me-1"></i> Desestimar</button>';
    html += '</div>';
    html += '</article>';

    return html;
}

export function renderPendientes() {
    var grid = $('#grid_pendientes');
    grid.empty();

    if (!store.precomprasData.length) {
        $('#contenido_pendientes').hide();
        $('#sin_pendientes').show();
        return;
    }

    $('#sin_pendientes').hide();

    $.each(store.precomprasData, function (i, precompra) {
        grid.append(buildCardPendiente(precompra));
    });

    $('#info_total').text(store.precomprasData.length + ' compra' + (store.precomprasData.length === 1 ? '' : 's') + ' pendiente' + (store.precomprasData.length === 1 ? '' : 's'));
    $('#contenido_pendientes').css('display', 'flex');
}

export function removerPrecompra(id) {
    store.precomprasData = store.precomprasData.filter(function (precompra) {
        return String(precompra.id) !== String(id);
    });
    renderPendientes();
}
