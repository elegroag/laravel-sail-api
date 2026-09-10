/**
 * Modulo ComprasPendientes - Precompras abandonadas (Mercurio)
 *
 * Punto de entrada (orquestador): cablea eventos del DOM e inicializa el flujo.
 * La logica esta separada por responsabilidad en modulos hermanos dentro de
 * este mismo directorio (store, constants, utils, datos, render, carga, pago,
 * desestimar).
 *
 * Lista las precompras en estado pendiente de pago (PE) y permite:
 *   - Verificar pago: reconsulta ePayco + guardar-venta si ya fue aceptado
 *   - Retomar el pago: valida la tarifa vigente y reabre el checkout de ePayco
 *     reutilizando la misma precompra (via sessionStorage epayco_precompra_id).
 *   - Desestimar: registra un motivo del catalogo (texto libre si es OTRO).
 *
 * Depende de globales cargados por el layout / blade:
 *   $ (jQuery), Swal (SweetAlert2), bootstrap, sessionStorage
 *   window.routes        -> rutas generadas con route() en el blade
 *   window.epaycoHandler -> handler configurado con ePayco.checkout.configure (v1)
 */
import store from './store.js';
import { MOTIVO_OTRO } from './constants.js';
import { buscarPrecompra } from './datos.js';
import { cargarDatos } from './carga.js';
import { retomarPago } from './pago.js';
import { verificarPago } from './revalidar.js';
import { abrirModalDesestimar, confirmarDesestimar } from './desestimar.js';

function bindHandlers() {
    var modalEl = document.getElementById('modal_desestimar');
    if (modalEl && typeof bootstrap !== 'undefined') {
        store.modalDesestimar = new bootstrap.Modal(modalEl);
    }

    $(document).on('click', '#btn_reintentar', function () {
        cargarDatos();
    });

    $(document).on('click', '.btn-verificar-pago', function () {
        var precompra = buscarPrecompra($(this).data('id'));
        if (precompra) {
            verificarPago(precompra);
        }
    });

    $(document).on('click', '.btn-retomar-pago', function () {
        var precompra = buscarPrecompra($(this).data('id'));
        if (precompra) {
            retomarPago(precompra);
        }
    });

    $(document).on('click', '.btn-desestimar', function () {
        var precompra = buscarPrecompra($(this).data('id'));
        if (precompra) {
            abrirModalDesestimar(precompra);
        }
    });

    $(document).on('change', 'input[name="motivo_desestimar"]', function () {
        var esOtro = $(this).val() === MOTIVO_OTRO;
        $('#grupo_detalle_otro').toggle(esOtro);
        $('#error_desestimar').hide();
        if (esOtro) {
            $('#txt_detalle_otro').trigger('focus');
        }
    });

    $(document).on('click', '#btn_confirmar_desestimar', confirmarDesestimar);
}

function init() {
    store.routes = window.routes || {};

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    bindHandlers();
    cargarDatos();
}

$(function () {
    init();
});
