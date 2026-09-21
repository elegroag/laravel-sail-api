/**
 * Panel de compra: beneficiario/servicio seleccionados, items multi-ben y limpieza.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';
import { obtenerCodben, formatearValor, escapeHtml } from './utils.js';
import { cerrarResumenCompraMovil, restaurarPanelAlSlot } from './vistaMovil.js';

export function actualizarPanelBeneficiario() {
    // Con ítems en el carrito, la lista del resumen reemplaza el bloque único.
    if (store.items && store.items.length > 0) {
        $('#panel_beneficiario').hide();
        $('#panel_beneficiario_nombre').text('');
        $('#panel_beneficiario_doc').text('');
        return;
    }

    var ben = store.beneficiarioSeleccionado;
    if (!ben) {
        $('#panel_beneficiario').hide();
        $('#panel_beneficiario_nombre').text('');
        $('#panel_beneficiario_doc').text('');
        return;
    }

    $('#panel_beneficiario_nombre').text(ben.nombre || '-');
    $('#panel_beneficiario_doc').text(obtenerCodben(ben));
    $('#panel_beneficiario').show();
}

export function limpiarSeleccionServicio() {
    store.servicioSeleccionado = null;
    store.items = [];
    store.revalidandoServicio = false;
    $('#hid_codser').val('');
    $('#hid_numero').val('');
    $('#hid_cupos_mes').val('');
    $('#hid_valor_raw').val('');
    $('#txt_cupos_mes').text('-').removeClass('panel-compra__cupos-mes--cero');
    $('#btn_procesar_pago').prop('disabled', false).show();
    $('#lista_items_compra').empty().hide();
    $('.servicio-card--selected').removeClass('servicio-card--selected');
    $('.beneficiario-card').removeClass('beneficiario-card--locked beneficiario-card--en-carrito');
    $('#seccion_servicios_activos').removeClass('servicios--bloqueados');
    cerrarResumenCompraMovil();
    restaurarPanelAlSlot();
    $('#panel_compra').hide();
    $('#btn_carrito_movil').hide();
    $('#detalle_tarifa').hide();
    $('#error_tarifa').hide();
    $('#panel_servicio_nombre').text('');
    $('#panel_servicio_descripcion').text('').hide();
}

export function actualizarPanelServicio(srv) {
    if (!srv) {
        $('#panel_servicio_nombre').text('');
        $('#panel_servicio_descripcion').text('').hide();
        return;
    }

    $('#panel_servicio_nombre').text(srv.nombre || '');

    var descripcion = srv.descripcion;
    if (descripcion !== null && descripcion !== undefined && String(descripcion).trim() !== '') {
        $('#panel_servicio_descripcion').text(descripcion).show();
    } else {
        $('#panel_servicio_descripcion').text('').hide();
    }
}

export function totalItems() {
    if (!store.items || store.items.length === 0) {
        return 0;
    }
    return store.items.reduce(function (acc, item) {
        return acc + (parseFloat(item.valser) || 0);
    }, 0);
}

export function renderListaItemsCompra() {
    var $lista = $('#lista_items_compra');
    if (!$lista.length) {
        return;
    }

    if (!store.items || store.items.length === 0) {
        $lista.empty().hide();
        return;
    }

    var html =
        '<div class="panel-compra__items-head">' +
            '<span class="panel-compra__items-title">Beneficiarios</span>' +
            '<span class="panel-compra__items-count">' + store.items.length + '</span>' +
        '</div>' +
        '<ul class="panel-compra__items list-unstyled mb-0">';

    store.items.forEach(function (item, idx) {
        html +=
            '<li class="panel-compra__item" data-idx="' + idx + '">' +
                '<div class="panel-compra__item-info">' +
                    '<span class="panel-compra__item-nombre">' + escapeHtml(item.nombre || item.codben) + '</span>' +
                    '<span class="panel-compra__item-doc">' + escapeHtml(item.codben) + '</span>' +
                '</div>' +
                '<div class="panel-compra__item-valor-wrap">' +
                    '<span class="panel-compra__item-valor">' + formatearValor(item.valser) + '</span>' +
                    (store.items.length > 1
                        ? '<button type="button" class="btn panel-compra__item-quitar btn-quitar-item" data-codben="' +
                            escapeHtml(item.codben) +
                            '" title="Quitar" aria-label="Quitar beneficiario">&times;</button>'
                        : '') +
                '</div>' +
            '</li>';
    });
    html += '</ul>';
    $lista.html(html).show();

    var total = totalItems();
    $('#txt_valor').text(formatearValor(total));
    $('#hid_valor_raw').val(String(total));

    marcarBeneficiariosEnCarrito();
}

export function quitarItemCompra(codben) {
    store.items = (store.items || []).filter(function (item) {
        return String(item.codben) !== String(codben);
    });

    if (store.items.length === 0) {
        limpiarSeleccionServicio();
        return;
    }

    renderListaItemsCompra();
    actualizarPanelBeneficiario();
}

export function marcarBeneficiariosEnCarrito() {
    var enCarrito = (store.items || []).map(function (i) { return String(i.codben); });
    $('.beneficiario-card').each(function () {
        var cod = String($(this).data('codben') || '');
        $(this).toggleClass('beneficiario-card--en-carrito', enCarrito.indexOf(cod) !== -1);
    });
}

/**
 * Payload items[] para precompra / guardar venta.
 */
export function itemsPayload() {
    return (store.items || []).map(function (item) {
        var row = { codben: String(item.codben) };
        if (item.nombre) {
            row.nombre = String(item.nombre);
        }
        if (item.valser !== undefined && item.valser !== null && item.valser !== '') {
            row.valser = parseFloat(item.valser) || 0;
        }
        return row;
    });
}
