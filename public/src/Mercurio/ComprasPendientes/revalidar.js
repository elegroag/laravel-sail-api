/**
 * Revalidar un pago ePayco desde compras pendientes (cuando guardar-venta
 * falló por error de conexión/API pero el pago pudo estar aceptado).
 */
import store from './store.js';
import { escapeHtml } from './utils.js';
import { removerPrecompra } from './render.js';
import { cargarDatos } from './carga.js';

export function verificarPago(precompra) {
    var refPayco = precompra && precompra.ref_payco ? String(precompra.ref_payco).trim() : '';
    if (!refPayco) {
        Swal.fire({
            title: 'Sin referencia',
            text: 'Esta compra no tiene referencia ePayco para verificar.',
            icon: 'warning',
            confirmButtonText: 'Entendido',
        });
        return;
    }

    Swal.fire({
        title: 'Verificando pago...',
        text: 'Consultando el estado en ePayco.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    $.ajax({
        url: store.routes.validarPagoEpayco,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            ref_payco: refPayco,
            precompra_id: precompra.id,
        },
    })
        .done(function (response) {
            if (!response.success || !response.data) {
                Swal.fire({
                    title: 'No se pudo verificar',
                    html: '<p>' + escapeHtml(response.message || 'Error al consultar ePayco') + '</p>',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                });
                return;
            }

            var datos = response.data;
            var codEstado = parseInt(datos.cod_estado, 10) || 0;
            var aprobado = datos.aprobado === true && codEstado === 1;

            if (!aprobado) {
                Swal.fire({
                    title: 'Pago no aprobado',
                    html:
                        '<p>Estado ePayco: <b>' +
                        escapeHtml(String(codEstado)) +
                        '</b></p>' +
                        '<p>Referencia: ' +
                        escapeHtml(datos.ref_payco || refPayco) +
                        '</p>' +
                        '<p>' +
                        escapeHtml(datos.motivo || datos.respuesta || 'Sin detalle') +
                        '</p>',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                });
                return;
            }

            Swal.fire({
                title: 'Pago aprobado',
                html: '<p>Registrando la venta...</p>',
                icon: 'success',
                showConfirmButton: false,
                allowOutsideClick: false,
            });

            registrarVentaPendiente(precompra, datos.ref_payco || refPayco);
        })
        .fail(function () {
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo verificar el pago con ePayco. Intente nuevamente.',
                icon: 'error',
                confirmButtonText: 'Entendido',
            });
        });
}

function registrarVentaPendiente(precompra, refpago) {
    var cedtra = $('#hid_documento').val() || precompra.documento || '';

    $.ajax({
        url: store.routes.guardarVenta,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            cedtra: cedtra,
            codser: precompra.codser,
            numero: precompra.numero,
            refpago: refpago,
            nota: precompra.nota || '',
            codben: precompra.codben || cedtra,
            precompra_id: precompra.id,
        },
    })
        .done(function (response) {
            if (response.success) {
                Swal.fire({
                    title: 'Compra registrada',
                    html: '<p>' + escapeHtml(response.message || 'Venta guardada exitosamente') + '</p>',
                    icon: 'success',
                    confirmButtonText: 'Entendido',
                }).then(function () {
                    removerPrecompra(precompra.id);
                    cargarDatos();
                });
                return;
            }

            Swal.fire({
                title: 'No se pudo registrar la venta',
                html: '<p>' + escapeHtml(response.message || 'Error al guardar la venta') + '</p>',
                icon: 'error',
                confirmButtonText: 'Entendido',
            });
        })
        .fail(function () {
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo guardar la venta. Intente verificar de nuevo.',
                icon: 'error',
                confirmButtonText: 'Entendido',
            });
        });
}
