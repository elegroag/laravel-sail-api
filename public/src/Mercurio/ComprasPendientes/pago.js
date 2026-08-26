/**
 * Retomar el pago de una precompra pendiente: valida tarifa vigente y reabre el
 * checkout de ePayco (Standard v1 o Smart v2) reutilizando la misma precompra.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2), ePayco,
 * sessionStorage.
 */
import store from './store.js';
import {
    escapeHtml,
    sanitizarTexto,
    obtenerEpaycoHandler,
    esCheckoutV2,
    epaycoTestActivo,
    tipoCheckoutV2,
    asegurarSdkCheckoutV2,
} from './utils.js';
import { nombreServicio } from './datos.js';
import { cargarDatos } from './carga.js';

export function retomarPago(precompra) {
    var cedtra = $('#hid_documento').val();
    var epaycoHandler = obtenerEpaycoHandler();

    if (!esCheckoutV2() && !epaycoHandler) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudo inicializar la pasarela de pago. Recargue la página.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    Swal.fire({
        title: 'Validando tarifa...',
        text: 'Consultando la disponibilidad y el valor vigente del servicio.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });

    $.ajax({
        url: store.routes.validarTarifa,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            cedtra: cedtra,
            codser: precompra.codser,
            numero: precompra.numero,
            codben: precompra.codben || cedtra
        }
    }).done(function (response) {
        if (!response.success) {
            Swal.fire({
                title: 'Servicio no disponible',
                html: '<p>' + escapeHtml(response.message || 'No se pudo validar la tarifa del servicio.') + '</p>' +
                    '<p class="text-muted mb-0">Puede desestimar esta compra si ya no desea continuar.</p>',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        var data = response.data || {};
        var valor = data.valser;

        if (!valor || parseFloat(valor) <= 0) {
            Swal.fire({
                title: 'Atención',
                text: 'No se pudo obtener el valor vigente del servicio.',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        var cuposMes = null;
        if (data.cupos_mes !== undefined && data.cupos_mes !== null && data.cupos_mes !== '') {
            cuposMes = parseInt(data.cupos_mes, 10) || 0;
        }

        if (cuposMes === 0) {
            Swal.fire({
                title: 'Sin cupos disponibles',
                html: '<p>No hay cupos disponibles para este servicio en el mes actual.</p>' +
                    '<p class="text-muted mb-0">Puede desestimar esta compra si ya no desea continuar.</p>',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        Swal.close();
        abrirCheckout(precompra, valor);
    }).fail(function () {
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo validar la tarifa del servicio. Intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    });
}

function abrirCheckout(precompra, valor) {
    var cedtra = $('#hid_documento').val();
    var epaycoHandler = obtenerEpaycoHandler();
    var servicioNombre = sanitizarTexto(nombreServicio(precompra)) || 'Compra de servicio';
    var nombre = sanitizarTexto((store.trabajadorData && store.trabajadorData.nombre) ? store.trabajadorData.nombre : 'Cliente');
    var email = (store.trabajadorData && store.trabajadorData.email) ? store.trabajadorData.email.trim() : 'sin@email.com';
    var invoice = 'ORD' + Date.now();

    // Reutilizar la misma precompra: el flujo del catalogo la retomara al validar el pago
    sessionStorage.setItem('epayco_cedtra', cedtra);
    sessionStorage.setItem('epayco_codser', String(precompra.codser));
    sessionStorage.setItem('epayco_numero', String(precompra.numero));
    sessionStorage.setItem('epayco_nota', precompra.nota || '');
    sessionStorage.setItem('epayco_codben', precompra.codben || cedtra);
    sessionStorage.setItem('epayco_precompra_id', String(precompra.id));

    if (esCheckoutV2()) {
        abrirCheckoutV2(precompra, valor, servicioNombre, nombre, email, cedtra);
        return;
    }

    var data = {
        name: servicioNombre,
        description: servicioNombre,
        invoice: invoice,
        currency: 'cop',
        amount: String(valor),
        tax_base: '0',
        tax: '0',
        country: 'co',
        lang: 'es',
        external: 'false',
        extra1: cedtra,
        extra2: String(precompra.codser),
        extra3: String(precompra.numero),
        extra4: String(precompra.id),
        response: store.routes.catalogo,
        confirmation: store.routes.epaycoConfirmation || '',
        name_billing: nombre,
        type_doc_billing: 'cc',
        number_doc_billing: cedtra,
        email_billing: email
    };

    epaycoHandler.onCloseModal = function () {
        setTimeout(function () {
            marcarPrecompraAbandonadaV2(precompra.id);
        }, 2500);
    };

    epaycoHandler.open(data);
}

// Smart Checkout v2: crea la sesion en backend reutilizando la precompra pendiente.
function abrirCheckoutV2(precompra, valor, servicioNombre, nombre, email, cedtra) {
    asegurarSdkCheckoutV2(function (errSdk) {
        if (errSdk) {
            Swal.fire({
                title: 'Error',
                text: errSdk.message || 'No se pudo cargar la pasarela de pago.',
                icon: 'error',
                confirmButtonText: 'Entendido',
            });
            return;
        }

        $.ajax({
            url: store.routes.crearSesionEpayco,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                cedtra: cedtra,
                codser: precompra.codser,
                numero: precompra.numero,
                codben: precompra.codben || cedtra,
                nota: precompra.nota || '',
                valor: valor,
                nombre_servicio: servicioNombre,
                nombre: nombre,
                email: email,
                precompra_id: precompra.id,
            },
        })
            .done(function (response) {
                if (response.success && response.data && response.data.sessionId) {
                    sessionStorage.setItem('epayco_precompra_id', String(response.data.precompra_id));
                    window.__epaycoPagoEnValidacion = false;

                    var tipo = tipoCheckoutV2();
                    var checkout;
                    try {
                        checkout = ePayco.checkout.configure({
                            sessionId: response.data.sessionId,
                            type: tipo,
                            test: epaycoTestActivo(),
                        });
                    } catch (e) {
                        console.log('Error inicializando ePayco v2:', e);
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo inicializar la pasarela de pago. Recargue la página.',
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                        });
                        return;
                    }

                    if (tipo === 'onpage') {
                        if (typeof checkout.onClosed === 'function') {
                            checkout.onClosed(function () {
                                setTimeout(function () {
                                    marcarPrecompraAbandonadaV2(precompra.id);
                                }, 2500);
                            });
                        }
                        if (typeof checkout.onErrors === 'function') {
                            checkout.onErrors(function (errores) {
                                console.log('ePayco onErrors:', errores);
                            });
                        }
                        checkout.open();
                        return;
                    }

                    Swal.fire({
                        title: 'Continuar con el pago',
                        html:
                            '<p>Será redirigido al entorno seguro de ePayco para completar el pago.</p>' +
                            '<p class="mb-0 text-muted">Al finalizar volverá a esta aplicación; la confirmación también se procesa en el servidor.</p>',
                        icon: 'info',
                        confirmButtonText: 'Continuar',
                    }).then(function () {
                        checkout.open();
                    });
                } else {
                    Swal.fire({
                        title: 'No se pudo iniciar el pago',
                        text: response.message || 'Error al crear la sesión de pago. Intente nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                    });
                }
            })
            .fail(function () {
                Swal.fire({
                    title: 'Error de conexión',
                    text: 'No se pudo crear la sesión de pago. Intente nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                });
            });
    });
}

function marcarPrecompraAbandonadaV2(precompraId) {
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

        sessionStorage.removeItem('epayco_cedtra');
        sessionStorage.removeItem('epayco_codser');
        sessionStorage.removeItem('epayco_numero');
        sessionStorage.removeItem('epayco_nota');
        sessionStorage.removeItem('epayco_codben');
        sessionStorage.removeItem('epayco_precompra_id');

        Swal.fire({
            title: 'Pago no completado',
            html:
                '<p>Cerraste la pasarela sin finalizar el pago.</p>' +
                '<p class="mb-0">La compra quedó marcada como <b>abandonada</b> y no se puede retomar.</p>',
            icon: 'info',
            confirmButtonText: 'Entendido',
        }).then(function () {
            cargarDatos();
        });
    });
}
