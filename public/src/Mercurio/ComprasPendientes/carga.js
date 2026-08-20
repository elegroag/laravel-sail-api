/**
 * Carga inicial: precompras pendientes, catalogo de servicios y trabajador.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';
import { mostrarLoader, ocultarLoader } from './utils.js';
import { renderPendientes } from './render.js';

export function cargarDatos() {
    var cedtra = $('#hid_documento').val();

    mostrarLoader('loader_pendientes');
    $('#error_pendientes').hide();
    $('#sin_pendientes').hide();
    $('#contenido_pendientes').hide();

    var reqPrecompras = $.ajax({
        url: store.routes.listarPrecompras,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {}
    });

    var reqServicios = $.ajax({
        url: store.routes.listarServicios,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {}
    });

    var reqTrabajador = $.ajax({
        url: store.routes.identificarTrabajador,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: { cedtra: cedtra }
    });

    $.when(reqPrecompras, reqServicios, reqTrabajador).done(function (resPrecompras, resServicios, resTrabajador) {
        ocultarLoader('loader_pendientes');

        var precompras = resPrecompras[0];
        if (!precompras.success) {
            $('#error_mensaje').text(precompras.message || 'Error al cargar las compras pendientes');
            $('#error_pendientes').show();
            return;
        }

        store.precomprasData = precompras.data || [];

        var servicios = resServicios[0];
        store.serviciosData = (servicios.success && servicios.data) ? servicios.data : [];

        var trabajador = resTrabajador[0];
        if (trabajador.success && trabajador.data) {
            var rawTrabajador = trabajador.data.trabajador || trabajador.data;
            if (rawTrabajador && rawTrabajador.trabajador && !rawTrabajador.cedtra) {
                store.trabajadorData = rawTrabajador.trabajador;
            } else {
                store.trabajadorData = rawTrabajador;
            }
        }

        renderPendientes();
    }).fail(function () {
        ocultarLoader('loader_pendientes');
        $('#error_mensaje').text('Error de conexión al consultar las compras pendientes');
        $('#error_pendientes').show();
    });
}
