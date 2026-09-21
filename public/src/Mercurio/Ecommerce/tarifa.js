/**
 * Validacion de tarifa del servicio seleccionado y acumulacion multi-beneficiario.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2).
 */
import store from './store.js';
import { formatearValor, mostrarLoader, ocultarLoader, mostrarNoty } from './utils.js';
import { obtenerCuposMes, actualizarCuposMesResumen, mostrarAlertaCuposMesCero } from './cupos.js';
import {
    actualizarPanelBeneficiario,
    actualizarPanelServicio,
    limpiarSeleccionServicio,
    renderListaItemsCompra,
    totalItems,
} from './panelCompra.js';
import { mostrarPanelCompra, actualizarFabCarrito } from './vistaMovil.js';

/**
 * Llama a validar-tarifa sin mutar el carrito.
 *
 * @returns {Promise<{ok:boolean,codben:string,data?:object,message?:string}>}
 */
function requestValidarTarifa(codser, numero, codben) {
    var cedtra = $('#hid_documento').val();

    return new Promise(function (resolve) {
        $.ajax({
            url: store.routes.validarTarifa,
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            data: {
                cedtra: cedtra,
                codser: codser,
                numero: numero,
                codben: codben,
            },
        })
            .done(function (response) {
                resolve({
                    ok: !!(response && response.success),
                    codben: String(codben),
                    data: response && response.data ? response.data : null,
                    message: (response && response.message) || '',
                });
            })
            .fail(function () {
                resolve({
                    ok: false,
                    codben: String(codben),
                    data: null,
                    message: 'Error de conexion al validar tarifa',
                });
            });
    });
}

function itemDesdeRespuesta(codben, nombreFallback, data, cuposMes) {
    return {
        codben: String(codben),
        nombre: (data && data.nombre) || nombreFallback || codben,
        tipben: (data && data.tipben) || '',
        valser: parseFloat(data && data.valser) || 0,
        categoria: (data && data.categoria) || '',
        cupos_disponibles: cuposMes !== null
            ? cuposMes
            : (parseInt(data && data.cupos_disponibles, 10) || 0),
    };
}

function notificarExcluidos(excluidos) {
    if (!excluidos || excluidos.length === 0) {
        return;
    }

    var lineas = excluidos.map(function (ex) {
        return '• ' + (ex.nombre || ex.codben) + ': ' + (ex.motivo || 'No cumple validación');
    }).join('<br>');

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Beneficiarios excluidos',
            html: '<p>No cumplen con el nuevo servicio:</p><p style="text-align:left">' + lineas + '</p>',
            icon: 'warning',
            confirmButtonText: 'Entendido',
        });
        return;
    }

    mostrarNoty('warning', excluidos.length + ' beneficiario(s) excluidos al cambiar de servicio.');
}

/**
 * Revalida en secuencia cada ítem del carrito contra un servicio nuevo.
 * Los que fallan se excluyen; el total se recalcula con los que pasan.
 */
export function revalidarItemsConServicio(srv, itemsPrevios) {
    var pendientes = (itemsPrevios || []).slice();
    var nuevos = [];
    var excluidos = [];
    var cuposMes = null;

    store.revalidandoServicio = true;

    $('#detalle_tarifa').hide();
    $('#error_tarifa').hide();
    $('#btn_procesar_pago').prop('disabled', true);
    mostrarLoader('loader_tarifa');

    $('#hid_codser').val(srv.codser);
    $('#hid_numero').val(srv.numero);

    function finalizar() {
        store.revalidandoServicio = false;
        ocultarLoader('loader_tarifa');
        $('#btn_procesar_pago').prop('disabled', false);

        store.items = nuevos;

        if (nuevos.length === 0) {
            notificarExcluidos(excluidos);
            mostrarNoty('error', 'Ningún beneficiario del carrito cumple con el nuevo servicio.');
            limpiarSeleccionServicio();
            return;
        }

        var ultimo = nuevos[nuevos.length - 1];
        actualizarCuposMesResumen(cuposMes !== null ? cuposMes : (ultimo.cupos_disponibles || 0));

        var total = totalItems();
        $('#txt_valor').text(formatearValor(total));
        $('#hid_valor_raw').val(String(total));
        $('#txt_tarifa_categoria').val(ultimo.categoria || '');
        $('#txt_tarifa_cupos').val(String(ultimo.cupos_disponibles || '0'));

        $('#detalle_tarifa').fadeIn();
        actualizarPanelBeneficiario();
        actualizarPanelServicio(srv);
        renderListaItemsCompra();
        mostrarPanelCompra();
        actualizarFabCarrito();

        mostrarNoty(
            'success',
            'Servicio actualizado. ' + nuevos.length + ' beneficiario(s) en el resumen.'
        );
        notificarExcluidos(excluidos);
    }

    function siguiente() {
        if (pendientes.length === 0) {
            finalizar();
            return;
        }

        if (cuposMes !== null && nuevos.length >= cuposMes) {
            while (pendientes.length > 0) {
                var sinCupo = pendientes.shift();
                excluidos.push({
                    codben: sinCupo.codben,
                    nombre: sinCupo.nombre,
                    motivo: 'Sin cupos suficientes para el servicio',
                });
            }
            finalizar();
            return;
        }

        var item = pendientes.shift();
        requestValidarTarifa(srv.codser, srv.numero, item.codben).then(function (res) {
            if (!res.ok || !res.data) {
                excluidos.push({
                    codben: item.codben,
                    nombre: item.nombre,
                    motivo: res.message || 'No cumple con los requisitos del servicio',
                });
                siguiente();
                return;
            }

            var c = obtenerCuposMes(res.data, srv);
            if (c !== null) {
                cuposMes = c;
            }

            if (c === 0 || (cuposMes !== null && nuevos.length >= cuposMes)) {
                excluidos.push({
                    codben: item.codben,
                    nombre: item.nombre,
                    motivo: c === 0
                        ? 'Sin cupos disponibles este mes'
                        : 'Sin cupos suficientes para el servicio',
                });
                siguiente();
                return;
            }

            nuevos.push(itemDesdeRespuesta(item.codben, item.nombre, res.data, cuposMes));
            siguiente();
        });
    }

    siguiente();
}

/**
 * Tras un fallo al agregar/validar, mantiene visible el resumen si ya hay ítems.
 */
function restaurarResumenSiHayItems() {
    if (!store.items || store.items.length === 0) {
        return false;
    }

    $('#detalle_tarifa').show();
    actualizarPanelBeneficiario();
    actualizarPanelServicio(store.servicioSeleccionado);
    renderListaItemsCompra();
    mostrarPanelCompra();
    actualizarFabCarrito();
    return true;
}

export function validarTarifa(codser, numero, codbenOverride) {
    var cedtra = $('#hid_documento').val();
    var codben = codbenOverride || $('#hid_codben').val() || cedtra;
    var tieneItems = !!(store.items && store.items.length > 0);

    // No ocultar el resumen si ya hay beneficiarios en la compra.
    if (!tieneItems) {
        $('#detalle_tarifa').hide();
    }
    $('#error_tarifa').hide();
    if (!tieneItems) {
        $('#txt_cupos_mes').text('-').removeClass('panel-compra__cupos-mes--cero');
    }
    $('#btn_procesar_pago').prop('disabled', false).show();
    mostrarLoader('loader_tarifa');

    $('#hid_codser').val(codser);
    $('#hid_numero').val(numero);

    requestValidarTarifa(codser, numero, codben).then(function (response) {
        ocultarLoader('loader_tarifa');

        if (response.ok && response.data) {
            var data = response.data;
            var cuposMes = obtenerCuposMes(data, store.servicioSeleccionado);

            var yaEnCarrito = (store.items || []).some(function (item) {
                return String(item.codben) === String(codben);
            });
            if (yaEnCarrito) {
                mostrarNoty('warning', 'Este beneficiario ya está en el resumen de compra.');
                restaurarResumenSiHayItems();
                return;
            }

            if (cuposMes !== null && store.items && store.items.length >= cuposMes) {
                mostrarNoty('warning', 'No hay cupos suficientes para agregar otro beneficiario.');
                restaurarResumenSiHayItems();
                return;
            }

            var puedeComprar = actualizarCuposMesResumen(cuposMes);

            if (!puedeComprar) {
                var msgCupos = 'No hay cupos disponibles para este servicio en el mes actual.';
                if (restaurarResumenSiHayItems()) {
                    mostrarNoty('warning', msgCupos + ' El resumen de compra se mantiene.');
                    return;
                }
                mostrarNoty('warning', 'No cumple con los requisitos minimos para aplicar al servicio.');
                limpiarSeleccionServicio();
                mostrarAlertaCuposMesCero();
                return;
            }

            var nombreBen = (store.beneficiarioSeleccionado && store.beneficiarioSeleccionado.nombre)
                ? store.beneficiarioSeleccionado.nombre
                : (data.nombre || codben);

            store.items = store.items || [];
            store.items.push(itemDesdeRespuesta(codben, nombreBen, data, cuposMes));

            var total = totalItems();
            $('#txt_valor').text(formatearValor(total));
            $('#hid_valor_raw').val(String(total));
            $('#txt_tarifa_categoria').val(data.categoria || '');
            $('#txt_temporada').val(data.temporada || '');
            $('#txt_tarifa_cupos').val(data.cupos_disponibles || '0');

            $('#detalle_tarifa').fadeIn();
            actualizarPanelBeneficiario();
            actualizarPanelServicio(store.servicioSeleccionado);
            renderListaItemsCompra();
            mostrarPanelCompra();

            var nombreServicio = (store.servicioSeleccionado && store.servicioSeleccionado.nombre)
                ? store.servicioSeleccionado.nombre
                : 'el servicio seleccionado';
            mostrarNoty('success', 'Se agregó "' + nombreServicio + '" para ' + nombreBen + '.');
            actualizarFabCarrito();
        } else {
            var motivo = response.message || 'No cumple con los requisitos minimos para aplicar al servicio.';

            if (restaurarResumenSiHayItems()) {
                mostrarNoty('warning', motivo + ' No se agregó al resumen.');
                // Quitar selección del ben que falló; el carrito se conserva.
                store.beneficiarioSeleccionado = null;
                $('#hid_codben').val('');
                $('.beneficiario-card--selected').removeClass('beneficiario-card--selected');
                return;
            }

            mostrarNoty('error', motivo);
            limpiarSeleccionServicio();
        }
    });
}
