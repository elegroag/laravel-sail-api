/**
 * Confirmacion de datos de contacto (email / telefono) antes de procesar el pago.
 * Si el usuario decide cambiarlos, se actualizan en Subsidio (actualiza_trabajador)
 * y solo con respuesta exitosa se continua con el pago.
 *
 * Depende de globales del layout: $ (jQuery), bootstrap.
 */
import store from './store.js';
import { mostrarNoty, escapeHtml } from './utils.js';
import { esVistaMovil, cerrarResumenCompraMovil } from './vistaMovil.js';

var EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
var TELEFONO_REGEX = /^[0-9]{7,10}$/;

var modalContacto = null;
var callbacks = { onConfirmar: null, onCancelar: null };
var confirmado = false;
var requiereAceptarZona = false;

function datosActuales() {
    var trabajador = store.trabajadorData || {};
    return {
        email: (trabajador.email || '').trim(),
        telefono: String(trabajador.telefono || '').trim(),
    };
}

function mostrarError(mensaje) {
    $('#error_contacto').text(mensaje).show();
}

function ocultarError() {
    $('#error_contacto').text('').hide();
}

function prepararAvisoZona() {
    var srv = store.servicioSeleccionado || {};
    var zona = String(srv.uis_ciudad || '').trim();

    requiereAceptarZona = zona !== '';
    $('#chk_acepta_zona').prop('checked', false);

    if (!requiereAceptarZona) {
        $('#aviso_zona_servicio').hide();
        return;
    }

    $('#txt_aviso_zona').html(
        'El servicio <strong>' + escapeHtml(srv.nombre || '') + '</strong> solo se brindará en la zona <strong>' + escapeHtml(zona) + '</strong>.'
    );
    $('#aviso_zona_servicio').show();
}

function actualizarEstadoBoton() {
    var zonaOk = !requiereAceptarZona || $('#chk_acepta_zona').is(':checked');
    $('#btn_confirmar_contacto').prop('disabled', !zonaOk);
}

function setFormEdicionVisible(visible) {
    var form = $('#form_cambiar_contacto');
    if (visible) {
        form.addClass('is-open').css('display', 'grid').removeAttr('hidden');
    } else {
        form.removeClass('is-open').css('display', 'none').attr('hidden', true);
    }
}

function resetFormulario() {
    prepararAvisoZona();
    var actuales = datosActuales();
    $('#txt_contacto_email').text(actuales.email || 'Sin correo registrado');
    $('#txt_contacto_telefono').text(actuales.telefono || 'Sin teléfono registrado');
    $('#chk_cambiar_contacto').prop('checked', false);
    setFormEdicionVisible(false);
    $('#inp_contacto_email').val(actuales.email);
    $('#inp_contacto_email_confirmacion').val('');
    $('#inp_contacto_telefono').val(actuales.telefono);
    ocultarError();
    actualizarEstadoBoton();
}

function validarFormulario() {
    var email = $('#inp_contacto_email').val().trim();
    var confirmacion = $('#inp_contacto_email_confirmacion').val().trim();
    var telefono = $('#inp_contacto_telefono').val().trim();

    if (!EMAIL_REGEX.test(email)) {
        return { error: 'Ingrese un correo electrónico válido.' };
    }
    if (email.toLowerCase() !== confirmacion.toLowerCase()) {
        return { error: 'La confirmación del correo no coincide.' };
    }
    if (!TELEFONO_REGEX.test(telefono)) {
        return { error: 'El teléfono debe tener entre 7 y 10 dígitos numéricos.' };
    }
    return { email: email, telefono: telefono };
}

function finalizarConfirmacion() {
    confirmado = true;
    modalContacto.hide();
}

function confirmarContacto(e) {
    e.preventDefault();
    ocultarError();

    if (requiereAceptarZona && !$('#chk_acepta_zona').is(':checked')) {
        mostrarError('Debe confirmar que entiende que el servicio solo se brindará en la zona indicada.');
        return;
    }

    if (!$('#chk_cambiar_contacto').is(':checked')) {
        finalizarConfirmacion();
        return;
    }

    var datos = validarFormulario();
    if (datos.error) {
        mostrarError(datos.error);
        return;
    }

    var boton = $('#btn_confirmar_contacto');
    boton.prop('disabled', true);

    $.ajax({
        url: store.routes.actualizarContacto,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            email: datos.email,
            email_confirmation: $('#inp_contacto_email_confirmacion').val().trim(),
            telefono: datos.telefono,
        },
    }).done(function (response) {
        if (response.success) {
            store.trabajadorData = $.extend({}, store.trabajadorData, {
                email: datos.email,
                telefono: datos.telefono,
            });
            mostrarNoty('success', response.message || 'Datos de contacto actualizados');
            finalizarConfirmacion();
            return;
        }
        actualizarEstadoBoton();
        mostrarError(response.message || 'No fue posible actualizar los datos de contacto.');
    }).fail(function () {
        actualizarEstadoBoton();
        mostrarError('Error de conexión al actualizar los datos de contacto. Intente nuevamente.');
    });
}

export function initConfirmacionContacto() {
    var modalEl = document.getElementById('modal_confirmar_contacto');
    if (!modalEl || typeof bootstrap === 'undefined') {
        return;
    }

    modalContacto = new bootstrap.Modal(modalEl, {
        backdrop: 'static',
        keyboard: false,
    });

    $('#chk_acepta_zona').on('change', function () {
        ocultarError();
        actualizarEstadoBoton();
    });

    $('#chk_cambiar_contacto').on('change', function () {
        ocultarError();
        setFormEdicionVisible(this.checked);
    });

    $('#inp_contacto_telefono').on('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    $('#btn_confirmar_contacto').on('click', confirmarContacto);

    $(modalEl).on('hidden.bs.modal', function () {
        var callback = confirmado ? callbacks.onConfirmar : callbacks.onCancelar;
        callbacks = { onConfirmar: null, onCancelar: null };
        confirmado = false;
        if (typeof callback === 'function') {
            callback();
        }
    });
}

/**
 * Abre la ventana de confirmacion. onConfirmar se ejecuta al cerrar la ventana
 * con los datos confirmados (y actualizados si el usuario lo eligio); onCancelar
 * si la cierra sin confirmar.
 */
export function confirmarDatosContacto(onConfirmar, onCancelar) {
    if (!modalContacto) {
        onConfirmar();
        return;
    }

    callbacks = { onConfirmar: onConfirmar, onCancelar: onCancelar };
    confirmado = false;
    resetFormulario();

    // En movil el panel vive dentro de #modal_resumen_compra: se cierra antes para no apilar modales.
    if (esVistaMovil() && $('#modal_resumen_compra').hasClass('show')) {
        $('#modal_resumen_compra').one('hidden.bs.modal', function () {
            modalContacto.show();
        });
        cerrarResumenCompraMovil();
        return;
    }

    modalContacto.show();
}
