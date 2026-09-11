/**
 * Identificacion del trabajador y render/seleccion del nucleo familiar.
 *
 * Depende de globales del layout: $ (jQuery).
 */
import store from './store.js';
import { mostrarLoader, ocultarLoader, escapeHtml, obtenerCodben, obtenerTipoBeneficiario } from './utils.js';
import { actualizarPanelBeneficiario, limpiarSeleccionServicio } from './panelCompra.js';
import { cargarServicios } from './serviciosRender.js';
import { esVistaMovil } from './vistaMovil.js';

export function identificarTrabajador() {
    var cedtra = $('#hid_documento').val();

    mostrarLoader('loader_trabajador');
    $('#error_trabajador').hide();
    $('#formulario_servicio').hide();

    $.ajax({
        url: store.routes.identificarTrabajador,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: { cedtra: cedtra }
    }).done(function (response) {
        ocultarLoader('loader_trabajador');

        if (response.success) {
            var rawTrabajador = response.data.trabajador || response.data;
            if (rawTrabajador && rawTrabajador.trabajador && !rawTrabajador.cedtra) {
                store.nucleoFamiliar = rawTrabajador.nucleo_familiar || [];
                store.trabajadorData = rawTrabajador.trabajador;
            } else {
                store.trabajadorData = rawTrabajador;
                store.nucleoFamiliar = response.data.nucleo_familiar || [];
            }
            renderBeneficiariosGrid(store.nucleoFamiliar, store.trabajadorData.detcat);
            $('#formulario_servicio').css('display', 'flex');
            cargarServicios();
        } else {
            $('#error_mensaje').text(response.message || 'Trabajador no encontrado');
            $('#error_trabajador').fadeIn();
        }
    }).fail(function () {
        ocultarLoader('loader_trabajador');
        $('#error_mensaje').text('Error de conexion al cargar beneficiarios y servicios');
        $('#error_trabajador').fadeIn();
    });
}

export function renderBeneficiariosGrid(nucleo, detcat) {
    var grid = $('#grid_beneficiarios');
    grid.empty();

    if (!nucleo || nucleo.length === 0) {
        $('#sin_beneficiarios').show();
        return;
    }

    $('#sin_beneficiarios').hide();

    $.each(nucleo, function (i, ben) {
        var codben = obtenerCodben(ben);
        var tipo = obtenerTipoBeneficiario(ben);
        var categoria = detcat || '';

        var card = $(
            '<button type="button" class="beneficiario-card" role="option" data-codben="' + escapeHtml(codben) + '">' +
                '<span class="beneficiario-card__tipo">' + escapeHtml(tipo) + '</span>' +
                '<span class="beneficiario-card__nombre">' + escapeHtml(ben.nombre || '') + '</span>' +
                '<span class="beneficiario-card__meta">' +
                    '<span><i class="far fa-id-card"></i> ' + escapeHtml(codben) + '</span>' +
                    (ben.edad ? '<span><i class="fas fa-birthday-cake"></i> ' + escapeHtml(String(ben.edad)) + ' años</span>' : '') +
                    (categoria ? '<span>' + escapeHtml(categoria) + '</span>' : '') +
                '</span>' +
            '</button>'
        );
        card.data('ben', ben);
        grid.append(card);
    });

    if (nucleo.length === 1) {
        seleccionarBeneficiario(obtenerCodben(nucleo[0]), nucleo[0]);
    }
}

export function seleccionarBeneficiario(codben, benData) {
    if (!codben) {
        store.beneficiarioSeleccionado = null;
        $('#hid_codben').val('');
        $('.beneficiario-card--selected').removeClass('beneficiario-card--selected');
        limpiarSeleccionServicio();
        return;
    }

    store.beneficiarioSeleccionado = benData;
    $('#hid_codben').val(codben);
    $('.beneficiario-card').removeClass('beneficiario-card--selected');
    $('.beneficiario-card[data-codben="' + codben + '"]').addClass('beneficiario-card--selected');
    actualizarPanelBeneficiario();
    limpiarSeleccionServicio();
    scrollAServiciosActivosEnMovil();
}

/**
 * En viewport movil, lleva el foco visual a la seccion de servicios activos.
 */
function scrollAServiciosActivosEnMovil() {
    if (!esVistaMovil()) {
        return;
    }

    var seccion = document.getElementById('seccion_servicios_activos');
    if (!seccion || typeof seccion.scrollIntoView !== 'function') {
        return;
    }

    window.setTimeout(function () {
        seccion.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 80);
}
