/**
 * Panel de compra: beneficiario/servicio seleccionados y limpieza de seleccion.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';
import { obtenerCodben } from './utils.js';
import { cerrarResumenCompraMovil, restaurarPanelAlSlot } from './vistaMovil.js';

export function actualizarPanelBeneficiario() {
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
    $('#hid_codser').val('');
    $('#hid_numero').val('');
    $('#hid_cupos_mes').val('');
    $('#txt_cupos_mes').text('-').removeClass('panel-compra__cupos-mes--cero');
    $('#btn_procesar_pago').prop('disabled', false).show();
    $('.servicio-card--selected').removeClass('servicio-card--selected');
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
