/**
 * Modulo Ecommerce - Catalogo de servicios (Mercurio)
 *
 * Punto de entrada (orquestador): cablea eventos del DOM e inicializa el flujo.
 * La logica esta separada por responsabilidad en modulos hermanos dentro de
 * este mismo directorio (store, utils, vistaMovil, panelCompra, cupos, epayco,
 * serviciosFiltros, serviciosRender, tarifa, beneficiarios, venta, pago).
 *
 * Depende de globales cargados por el layout / blade:
 *   $ (jQuery), Swal (SweetAlert2), bootstrap, sessionStorage
 *   window.routes        -> rutas generadas con route() en el blade
 *   window.epaycoHandler -> handler configurado con ePayco.checkout.configure (v1)
 */
import store from './store.js';
import { obtenerCodben } from './utils.js';
import { limpiarSeleccionServicio } from './panelCompra.js';
import {
    esVistaMovil,
    restaurarPanelAlSlot,
    actualizarFabCarrito,
    abrirResumenCompraMovil,
    sincronizarVistaCompra,
} from './vistaMovil.js';
import { identificarTrabajador, seleccionarBeneficiario } from './beneficiarios.js';
import { filtrarServicios, seleccionarServicio } from './serviciosRender.js';
import { verificarRespuestaEpayco } from './epayco.js';
import { procesarPago } from './pago.js';

function bindHandlers() {
    var modalEl = document.getElementById('modal_resumen_compra');
    if (modalEl && typeof bootstrap !== 'undefined') {
        store.modalResumenCompra = new bootstrap.Modal(modalEl, {
            backdrop: 'static',
            keyboard: false,
        });
    }

    $('#btn_carrito_movil').on('click', abrirResumenCompraMovil);
    $('#btn_cancelar_resumen_compra').on('click', function (e) {
        e.preventDefault();
        limpiarSeleccionServicio();
    });

    $('#modal_resumen_compra').on('hidden.bs.modal', function () {
        restaurarPanelAlSlot();
        if (esVistaMovil()) {
            $('#panel_compra').hide();
            actualizarFabCarrito();
        }
    });

    var resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(sincronizarVistaCompra, 150);
    });

    $(document).on('click', '.beneficiario-card', function () {
        var ben = $(this).data('ben');
        var codben = obtenerCodben(ben);
        if ($(this).hasClass('beneficiario-card--selected')) {
            return;
        }
        seleccionarBeneficiario(codben, ben);
    });

    $(document).on('input', '#buscar_servicio', function () {
        filtrarServicios();
    });

    $(document).on('change', '#filtro_codser', function () {
        filtrarServicios();
    });

    $(document).on('click', '.servicio-card:not(.servicio-card--disabled)', function () {
        var srv = $(this).data('srv');
        if (!srv) return;
        seleccionarServicio(srv);
    });

    $(document).on('click', '#btn_procesar_pago', procesarPago);
}

function init() {
    store.routes = window.routes || {};

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    bindHandlers();
    identificarTrabajador();
    verificarRespuestaEpayco();
}

$(function () {
    init();
});
