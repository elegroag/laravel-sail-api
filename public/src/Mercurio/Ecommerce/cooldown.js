/**
 * Cooldown de compra: bloquea nuevas compras del mismo servicio + beneficiario
 * mientras exista una precompra pendiente reciente y muestra el tiempo restante.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';

var bloqueos = [];
var timer = null;

function formatearTiempo(segundos) {
    var m = Math.floor(segundos / 60);
    var s = segundos % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

// Segundos restantes del bloqueo que aplica al servicio y carrito actuales (0 si no hay).
export function segundosBloqueoActual() {
    var srv = store.servicioSeleccionado;
    if (!srv || !store.items || !store.items.length) {
        return 0;
    }

    var codser = String($('#hid_codser').val() || srv.codser || '');
    var numero = String($('#hid_numero').val() || srv.numero || '');
    var codbens = store.items.map(function (i) {
        return String(i.codben);
    });

    var max = 0;
    bloqueos.forEach(function (b) {
        if (String(b.codser) !== codser || String(b.numero) !== numero) {
            return;
        }
        var coincide = b.codbens.some(function (c) {
            return codbens.indexOf(String(c)) !== -1;
        });
        if (coincide && b.segundos_restantes > max) {
            max = b.segundos_restantes;
        }
    });
    return max;
}

export function aplicarEstadoCooldown() {
    var restante = segundosBloqueoActual();
    var $btn = $('#btn_procesar_pago');

    if (restante > 0) {
        $('#txt_cooldown_tiempo').text(formatearTiempo(restante));
        $('#aviso_cooldown_compra').show();
        $btn.prop('disabled', true);
    } else {
        $('#aviso_cooldown_compra').hide();
        if ($btn.data('cooldown') && $('#hid_cupos_mes').val() !== '0') {
            $btn.prop('disabled', false);
        }
    }
    $btn.data('cooldown', restante > 0);
}

export function estaBloqueado() {
    return segundosBloqueoActual() > 0;
}

function iniciarTimer() {
    if (timer) {
        return;
    }
    timer = setInterval(function () {
        var activos = false;
        bloqueos.forEach(function (b) {
            if (b.segundos_restantes > 0) {
                b.segundos_restantes -= 1;
                activos = true;
            }
        });
        aplicarEstadoCooldown();
        if (!activos) {
            clearInterval(timer);
            timer = null;
        }
    }, 1000);
}

export function cargarBloqueos() {
    if (!store.routes || !store.routes.bloqueosPrecompra) {
        return $.Deferred().resolve().promise();
    }

    return $.ajax({
        url: store.routes.bloqueosPrecompra,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
    }).done(function (response) {
        bloqueos = response.success && Array.isArray(response.data) ? response.data : [];
        aplicarEstadoCooldown();
        if (bloqueos.length) {
            iniciarTimer();
        }
    });
}

// Muestra el mensaje de cooldown devuelto por el backend y re-sincroniza.
export function manejarRespuestaCooldown(response) {
    if (!response || response.code !== 'PRECOMPRA_COOLDOWN') {
        return false;
    }

    Swal.fire({
        title: 'Pago pendiente',
        text: response.message,
        icon: 'warning',
        confirmButtonText: 'Entendido',
    });
    cargarBloqueos();
    return true;
}
