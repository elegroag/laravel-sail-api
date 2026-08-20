/**
 * Integracion con ePayco: Standard Checkout (v1) y Smart Checkout (v2),
 * validacion del pago tras el retorno y manejo de precompras abandonadas.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2), ePayco,
 * sessionStorage y las banderas window.EPAYCO_CHECKOUT_VERSION / window.EPAYCO_TEST.
 */
import store from './store.js';
import { ESTADOS_EPAYCO } from './constants.js';
import { limpiarSeleccionServicio } from './panelCompra.js';
import { guardarVenta } from './venta.js';

export function obtenerEpaycoHandler() {
    return window.epaycoHandler || null;
}

export function limpiarSessionEpayco() {
    sessionStorage.removeItem('epayco_cedtra');
    sessionStorage.removeItem('epayco_codser');
    sessionStorage.removeItem('epayco_numero');
    sessionStorage.removeItem('epayco_nota');
    sessionStorage.removeItem('epayco_codben');
    sessionStorage.removeItem('epayco_precompra_id');
}

export function marcarPagoEnValidacion(activo) {
    window.__epaycoPagoEnValidacion = !!activo;
}

export function marcarPrecompraAbandonada(precompraId) {
    if (!precompraId || !store.routes.abandonarPrecompra) {
        return;
    }

    if (window.__epaycoPagoEnValidacion) {
        return;
    }

    $.ajax({
        url: store.routes.abandonarPrecompra,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: { precompra_id: precompraId },
    }).done(function (response) {
        if (!response.success || !response.data || !response.data.abandonada) {
            return;
        }

        limpiarSessionEpayco();
        limpiarSeleccionServicio();

        Swal.fire({
            title: 'Pago no completado',
            html:
                '<p>Cerraste la pasarela sin finalizar el pago.</p>' +
                '<p class="mb-0">La compra quedó marcada como <b>abandonada</b> y no se puede retomar.</p>',
            icon: 'info',
            confirmButtonText: 'Entendido',
        });
    });
}

export function registrarOnCloseEpayco(epaycoHandler, precompraId) {
    if (!epaycoHandler || !precompraId) {
        return;
    }

    epaycoHandler.onCloseModal = function () {
        // Esperar a una posible redireccion/validacion post-pago antes de abandonar
        setTimeout(function () {
            marcarPrecompraAbandonada(precompraId);
        }, 2500);
    };
}

export function esCheckoutV2() {
    return String(window.EPAYCO_CHECKOUT_VERSION) === '2';
}

export function epaycoTestActivo() {
    return window.EPAYCO_TEST === true || String(window.EPAYCO_TEST) === 'true';
}

export function registrarHooksCheckoutV2(checkout, precompraId) {
    if (!checkout) {
        return;
    }
    if (typeof checkout.onClosed === 'function') {
        checkout.onClosed(function () {
            setTimeout(function () {
                marcarPrecompraAbandonada(precompraId);
            }, 2500);
        });
    }
    if (typeof checkout.onErrors === 'function') {
        checkout.onErrors(function (errores) {
            console.log('ePayco onErrors:', errores);
        });
    }
}

// Smart Checkout v2: crea la sesion en backend y abre el checkout con sessionId.
export function abrirCheckoutV2(ctx, target) {
    $.ajax({
        url: store.routes.crearSesionEpayco,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            cedtra: ctx.documento,
            codser: ctx.codser,
            numero: ctx.numero,
            codben: ctx.codben,
            nota: ctx.nota,
            valor: ctx.valor,
            nombre_servicio: ctx.servicioNombre,
            nombre: ctx.nombre,
            email: ctx.email
        }
    }).done(function (response) {
        if (response.success && response.data && response.data.sessionId) {
            sessionStorage.setItem('epayco_precompra_id', response.data.precompra_id);
            marcarPagoEnValidacion(false);

            var checkout;
            try {
                checkout = ePayco.checkout.configure({
                    sessionId: response.data.sessionId,
                    type: 'onpage',
                    test: epaycoTestActivo()
                });
            } catch (e) {
                console.log('Error inicializando ePayco v2:', e);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo inicializar la pasarela de pago. Recargue la pagina.',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                if (target) target.removeAttr('disabled');
                return;
            }

            registrarHooksCheckoutV2(checkout, response.data.precompra_id);
            checkout.open();
        } else {
            Swal.fire({
                title: 'No se pudo iniciar el pago',
                text: response.message || 'Error al crear la sesion de pago. Intente nuevamente.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
        if (target) target.removeAttr('disabled');
    }).fail(function () {
        Swal.fire({
            title: 'Error de conexion',
            text: 'No se pudo crear la sesion de pago. Intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        if (target) target.removeAttr('disabled');
    });
}

export function pagoEpaycoAprobado(datos) {
    return datos && parseInt(datos.cod_estado) === 1 && datos.aprobado === true;
}

export function obtenerTextoEstadoEpayco(codigo) {
    return ESTADOS_EPAYCO[codigo] || 'Desconocido (' + codigo + ')';
}

export function mostrarPagoNoRegistrado(codEstado, refPayco, motivo) {
    var estadoTexto = obtenerTextoEstadoEpayco(codEstado);
    limpiarSessionEpayco();

    Swal.fire({
        title: 'Pago no completado',
        html: '<p>Estado: <b>' + estadoTexto + '</b></p>' +
            '<p>Referencia: ' + (refPayco || '-') + '</p>' +
            (motivo ? '<p>Motivo: ' + motivo + '</p>' : '') +
            '<p class="text-muted mt-2">La venta no fue registrada.</p>',
        icon: 'warning',
        showConfirmButton: true,
        confirmButtonText: 'Entendido'
    });
}

export function verificarRespuestaEpayco() {
    var urlParams = window.location.search;
    var refPayco = '';
    var codEstadoUrl = 0;
    var motivoUrl = '';

    if (urlParams) {
        var params = new URLSearchParams(urlParams);
        refPayco = params.get('ref_payco') || params.get('refPayco') || params.get('x_ref_payco') || '';
        codEstadoUrl = parseInt(params.get('x_cod_transaction_state') || params.get('cod_transaction_state') || '0') || 0;
        motivoUrl = params.get('x_response_reason_text') || params.get('x_response') || '';
    }

    if (!refPayco) {
        var path = window.location.pathname;
        var match = path.match(/checkout\/([^/]+)\/response/);
        if (match) {
            refPayco = match[1];
        }
    }

    if (refPayco) {
        marcarPagoEnValidacion(true);

        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        if (codEstadoUrl && codEstadoUrl !== 1) {
            marcarPagoEnValidacion(false);
            mostrarPagoNoRegistrado(codEstadoUrl, refPayco, motivoUrl);
            return;
        }

        Swal.fire({
            title: 'Verificando pago...',
            html: '<p>Referencia: <b>' + refPayco + '</b></p><p>Consultando estado de la transaccion en ePayco...</p>',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
        });
        validarPagoEpayco(refPayco);
    }
}

export function validarPagoEpayco(refPayco) {
    $.ajax({
        url: store.routes.validarPagoEpayco,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            ref_payco: refPayco,
            precompra_id: sessionStorage.getItem('epayco_precompra_id') || 0
        }
    }).done(function (response) {
        if (response.success && response.data) {
            var datos = response.data;
            var codEstado = parseInt(datos.cod_estado) || 0;

            if (pagoEpaycoAprobado(datos)) {
                Swal.fire({
                    title: 'Pago aprobado',
                    html: '<p>Estado: <b>' + obtenerTextoEstadoEpayco(codEstado) + '</b></p>' +
                        '<p>Referencia: <b>' + (datos.ref_payco || refPayco) + '</b></p>' +
                        '<p>Monto: <b>$' + datos.monto + '</b></p>' +
                        '<p>Guardando la venta...</p>',
                    icon: 'success',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                guardarVenta(datos.ref_payco || refPayco);
                return;
            }

            mostrarPagoNoRegistrado(codEstado, refPayco, datos.motivo || datos.respuesta || 'Sin detalle');
        } else {
            limpiarSessionEpayco();
            Swal.fire({
                title: 'No se pudo verificar el pago',
                html: '<p>' + (response.message || 'Error al consultar ePayco') + '</p>' +
                    '<p>La venta no fue registrada.</p>',
                icon: 'error',
                showConfirmButton: true,
                confirmButtonText: 'Entendido'
            });
        }
    }).fail(function () {
        limpiarSessionEpayco();
        Swal.fire({
            title: 'Error de conexion',
            html: '<p>No se pudo verificar el pago con ePayco.</p>' +
                '<p>La venta no fue registrada.</p>',
            icon: 'error',
            showConfirmButton: true,
            confirmButtonText: 'Entendido'
        });
    });
}
