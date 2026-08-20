/**
 * Procesamiento del pago: valida datos, crea la precompra y abre el checkout
 * (Standard v1 o Smart v2 segun EPAYCO_CHECKOUT_VERSION).
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2), sessionStorage.
 */
import store from './store.js';
import { sanitizarTexto } from './utils.js';
import { obtenerCuposMes, mostrarAlertaCuposMesCero } from './cupos.js';
import {
    obtenerEpaycoHandler,
    esCheckoutV2,
    abrirCheckoutV2,
    marcarPagoEnValidacion,
    registrarOnCloseEpayco,
} from './epayco.js';

export function procesarPago(event) {
    event.preventDefault();
    var target = $(event.currentTarget);
    target.attr('disabled', true);

    var valor = $('#hid_valor_raw').val();
    if (!valor || parseFloat(valor) <= 0) {
        Swal.fire({
            title: 'Atencion',
            text: 'No se ha obtenido el valor del servicio',
            icon: 'warning',
            showConfirmButton: false,
            timer: 3000
        });
        target.removeAttr('disabled');
        return;
    }

    var cuposMes = obtenerCuposMes(null, store.servicioSeleccionado);
    if (cuposMes === null && $('#hid_cupos_mes').val() !== '') {
        cuposMes = parseInt($('#hid_cupos_mes').val(), 10) || 0;
    }

    if (cuposMes === 0) {
        mostrarAlertaCuposMesCero();
        target.removeAttr('disabled');
        return;
    }

    var epaycoHandler = obtenerEpaycoHandler();
    if (!esCheckoutV2() && !epaycoHandler) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudo inicializar la pasarela de pago. Recargue la pagina.',
            icon: 'error',
            showConfirmButton: false,
            timer: 5000
        });
        target.removeAttr('disabled');
        return;
    }

    var nombre = sanitizarTexto((store.trabajadorData && store.trabajadorData.nombre) ? store.trabajadorData.nombre : 'Cliente');
    var email = (store.trabajadorData && store.trabajadorData.email) ? store.trabajadorData.email.trim() : 'sin@email.com';
    var documento = $('#hid_documento').val();
    var invoice = 'ORD' + Date.now();
    var servicioNombre = 'Compra de servicio';

    if (store.servicioSeleccionado && store.servicioSeleccionado.nombre) {
        servicioNombre = sanitizarTexto(store.servicioSeleccionado.nombre);
    }

    var ctx = {
        documento: documento,
        codser: $('#hid_codser').val(),
        numero: $('#hid_numero').val(),
        codben: $('#hid_codben').val() || documento,
        nota: $('#txt_nota').val() || '',
        valor: valor,
        servicioNombre: servicioNombre,
        nombre: nombre,
        email: email
    };

    sessionStorage.setItem('epayco_cedtra', documento);
    sessionStorage.setItem('epayco_codser', ctx.codser);
    sessionStorage.setItem('epayco_numero', ctx.numero);
    sessionStorage.setItem('epayco_nota', ctx.nota);
    sessionStorage.setItem('epayco_codben', ctx.codben);

    if (esCheckoutV2()) {
        abrirCheckoutV2(ctx, target);
        return;
    }

    // ==== Standard Checkout v1 ====
    var data = {
        name: servicioNombre,
        description: servicioNombre,
        invoice: invoice,
        currency: 'cop',
        amount: valor,
        tax_base: '0',
        tax: '0',
        country: 'co',
        lang: 'es',
        external: 'false',
        extra1: documento,
        extra2: ctx.codser,
        extra3: ctx.numero,
        response: window.location.href,
        confirmation: store.routes.epaycoConfirmation || '',
        name_billing: nombre,
        type_doc_billing: 'cc',
        number_doc_billing: documento,
        email_billing: email
    };

    // Respaldar la precompra en base de datos antes de abrir la pasarela
    $.ajax({
        url: store.routes.crearPrecompra,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            cedtra: documento,
            codser: ctx.codser,
            numero: ctx.numero,
            codben: ctx.codben,
            nota: ctx.nota,
            valor: valor
        }
    }).done(function (response) {
        if (response.success && response.data && response.data.id) {
            sessionStorage.setItem('epayco_precompra_id', response.data.id);
            marcarPagoEnValidacion(false);
            registrarOnCloseEpayco(epaycoHandler, response.data.id);
            data.extra4 = String(response.data.id);
            if (store.routes.epaycoConfirmation) {
                data.confirmation = store.routes.epaycoConfirmation;
            }
            epaycoHandler.open(data);
        } else {
            Swal.fire({
                title: 'No se pudo iniciar el pago',
                text: response.message || 'Error al registrar la precompra. Intente nuevamente.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
        target.removeAttr('disabled');
    }).fail(function () {
        Swal.fire({
            title: 'Error de conexion',
            text: 'No se pudo registrar la precompra. Intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        target.removeAttr('disabled');
    });
}
