/**
 * Validacion de tarifa del servicio seleccionado y actualizacion del resumen.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';
import { formatearValor, mostrarLoader, ocultarLoader, mostrarNoty } from './utils.js';
import { obtenerCuposMes, actualizarCuposMesResumen, mostrarAlertaCuposMesCero } from './cupos.js';
import { actualizarPanelBeneficiario, actualizarPanelServicio, limpiarSeleccionServicio } from './panelCompra.js';
import { mostrarPanelCompra, actualizarFabCarrito } from './vistaMovil.js';

export function validarTarifa(codser, numero) {
    var cedtra = $('#hid_documento').val();

    $('#detalle_tarifa').hide();
    $('#error_tarifa').hide();
    $('#txt_cupos_mes').text('-').removeClass('panel-compra__cupos-mes--cero');
    $('#btn_procesar_pago').prop('disabled', false).show();
    mostrarLoader('loader_tarifa');

    $('#hid_codser').val(codser);
    $('#hid_numero').val(numero);

    $.ajax({
        url: store.routes.validarTarifa,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {
            cedtra: cedtra,
            codser: codser,
            numero: numero,
            codben: $('#hid_codben').val() || cedtra
        }
    }).done(function (response) {
        ocultarLoader('loader_tarifa');

        if (response.success) {
            var data = response.data;
            var cuposMes = obtenerCuposMes(data, store.servicioSeleccionado);

            $('#txt_valor').text(formatearValor(data.valser));
            $('#hid_valor_raw').val(data.valser);
            $('#txt_tarifa_categoria').val(data.categoria || '');
            $('#txt_temporada').val(data.temporada || '');
            $('#txt_tarifa_cupos').val(data.cupos_disponibles || '0');

            var puedeComprar = actualizarCuposMesResumen(cuposMes);

            if (!puedeComprar) {
                mostrarNoty(
                    'warning',
                    'No cumple con los requisitos minimos para aplicar al servicio.'
                );
                limpiarSeleccionServicio();
                mostrarAlertaCuposMesCero();
                return;
            }

            $('#detalle_tarifa').fadeIn();
            actualizarPanelBeneficiario();
            actualizarPanelServicio(store.servicioSeleccionado);
            mostrarPanelCompra();

            var nombreServicio = (store.servicioSeleccionado && store.servicioSeleccionado.nombre)
                ? store.servicioSeleccionado.nombre
                : 'el servicio seleccionado';
            mostrarNoty('success', 'Se agregó "' + nombreServicio + '" al resumen de compra.');
            actualizarFabCarrito();
        } else {
            mostrarNoty('error', 'No cumple con los requisitos minimos para aplicar al servicio.');
            limpiarSeleccionServicio();
        }
    }).fail(function () {
        ocultarLoader('loader_tarifa');
        mostrarNoty('error', 'Error de conexion al validar tarifa');
        limpiarSeleccionServicio();
    });
}
