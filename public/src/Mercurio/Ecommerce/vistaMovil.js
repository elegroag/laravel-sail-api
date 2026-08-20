/**
 * Vista movil del panel de compra: panel / FAB / modal de resumen.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2).
 */
import store from './store.js';

export function esVistaMovil() {
    return window.matchMedia('(max-width: 991.98px)').matches;
}

export function moverPanelAlModal() {
    $('#modal_resumen_compra_body').append($('#panel_compra'));
}

export function restaurarPanelAlSlot() {
    $('#panel_compra_slot').append($('#panel_compra'));
}

export function actualizarFabCarrito() {
    if (esVistaMovil() && store.servicioSeleccionado) {
        $('#btn_carrito_movil').css('display', 'flex');
    } else {
        $('#btn_carrito_movil').hide();
    }
}

export function mostrarPanelCompra() {
    if (esVistaMovil()) {
        if ($('#modal_resumen_compra').hasClass('show')) {
            cerrarResumenCompraMovil();
        } else {
            restaurarPanelAlSlot();
            $('#panel_compra').hide();
        }
        actualizarFabCarrito();
        return;
    }

    restaurarPanelAlSlot();
    $('#panel_compra').show();
    $('#btn_carrito_movil').hide();
}

export function abrirResumenCompraMovil() {
    if (!store.servicioSeleccionado) {
        Swal.fire({
            title: 'Atención',
            text: 'Seleccione un beneficiario y un servicio para ver el resumen.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    moverPanelAlModal();
    $('#panel_compra').show();

    if (store.modalResumenCompra) {
        store.modalResumenCompra.show();
    }
}

export function cerrarResumenCompraMovil() {
    if (store.modalResumenCompra && $('#modal_resumen_compra').hasClass('show')) {
        store.modalResumenCompra.hide();
    }
}

export function sincronizarVistaCompra() {
    if (!esVistaMovil()) {
        cerrarResumenCompraMovil();
        restaurarPanelAlSlot();
        if (store.servicioSeleccionado) {
            $('#panel_compra').show();
        } else {
            $('#panel_compra').hide();
        }
        $('#btn_carrito_movil').hide();
        return;
    }

    if ($('#modal_resumen_compra').hasClass('show')) {
        return;
    }

    restaurarPanelAlSlot();
    if (store.servicioSeleccionado) {
        $('#panel_compra').hide();
        actualizarFabCarrito();
    } else {
        $('#panel_compra').hide();
        $('#btn_carrito_movil').hide();
    }
}
