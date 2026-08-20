/**
 * Render del catalogo de servicios, carga desde backend y seleccion de servicio.
 *
 * Depende de globales del layout: $ (jQuery), Swal (SweetAlert2).
 */
import store from './store.js';
import { escapeHtml, mostrarLoader, ocultarLoader } from './utils.js';
import { servicioPasaFiltros, poblarFiltroCodser } from './serviciosFiltros.js';
import { limpiarSeleccionServicio } from './panelCompra.js';
import { validarTarifa } from './tarifa.js';

export function contarServiciosDisponibles(servicios, query, codser) {
    var count = 0;
    $.each(servicios, function (i, srv) {
        var cupos = parseInt(srv.cupos_disponibles, 10) || 0;
        if (cupos > 0 && servicioPasaFiltros(srv, query, codser)) {
            count++;
        }
    });
    return count;
}

export function renderServiciosGrid(servicios, query, codser) {
    var grid = $('#grid_servicios');
    grid.empty();
    query = query || '';
    codser = codser || '';
    store.busquedaServicio = query;
    store.filtroCodserServicio = codser;

    var visibles = 0;
    var disponibles = contarServiciosDisponibles(servicios, query, codser);

    $('#contador_servicios').text(
        disponibles + ' servicio' + (disponibles === 1 ? '' : 's') + ' disponible' + (disponibles === 1 ? '' : 's')
    );

    $.each(servicios, function (i, srv) {
        if (!servicioPasaFiltros(srv, query, codser)) {
            return;
        }

        visibles++;
        var cupos = parseInt(srv.cupos_disponibles, 10) || 0;
        var sinCupos = cupos <= 0;
        var valmes = srv.valmes === 'S';
        var cardKey = srv.codser + '|' + srv.numero;
        var ciudadHtml = srv.uis_ciudad
            ? '<span><i class="fas fa-map-marker-alt"></i> ' + escapeHtml(srv.uis_ciudad) + '</span>'
            : '';

        var card = $(
            '<button type="button" class="servicio-card' + (sinCupos ? ' servicio-card--disabled' : '') + '"' +
                ' role="option"' +
                ' data-key="' + escapeHtml(cardKey) + '"' +
                (sinCupos ? ' disabled' : '') + '>' +
                '<div class="servicio-card__header">' +
                    '<span class="servicio-card__badge' + (sinCupos ? ' servicio-card__badge--muted' : '') + '">' +
                        (sinCupos ? 'Sin cupos' : cupos + ' cupos') +
                    '</span>' +
                    (valmes ? '<span class="servicio-card__badge servicio-card__badge--info">Mensual</span>' : '') +
                '</div>' +
                '<h3 class="servicio-card__titulo">' + escapeHtml(srv.nombre || '') + '</h3>' +
                '<p class="servicio-card__detalle">' + escapeHtml(srv.detalle || '') + '</p>' +
                '<div class="servicio-card__meta">' +
                    ciudadHtml +
                    '<span><i class="fas fa-child"></i> ' + escapeHtml(String(srv.edadini)) + '–' + escapeHtml(String(srv.edadfin)) + ' años</span>' +
                    '<span><i class="far fa-calendar-alt"></i> ' + escapeHtml(srv.fecini || '') + ' – ' + escapeHtml(srv.fecfin || '') + '</span>' +
                '</div>' +
            '</button>'
        );

        card.data('srv', srv);
        grid.append(card);

        if (store.servicioSeleccionado &&
            String(store.servicioSeleccionado.codser) === String(srv.codser) &&
            String(store.servicioSeleccionado.numero) === String(srv.numero) &&
            !sinCupos) {
            card.addClass('servicio-card--selected');
        }
    });

    $('#sin_servicios').toggle(visibles === 0);
}

export function filtrarServicios() {
    var query = $('#buscar_servicio').val().trim();
    var codser = $('#filtro_codser').val();

    if (store.servicioSeleccionado && !servicioPasaFiltros(store.servicioSeleccionado, query, codser)) {
        limpiarSeleccionServicio();
    }

    renderServiciosGrid(store.serviciosData, query, codser);
}

export function cargarServicios() {
    mostrarLoader('loader_servicios');
    $('#contenedor_servicios').hide();

    $.ajax({
        url: store.routes.listarServicios,
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        data: {}
    }).done(function (response) {
        ocultarLoader('loader_servicios');

        if (response.success) {
            store.serviciosData = response.data || [];
            poblarFiltroCodser(store.serviciosData);
            renderServiciosGrid(store.serviciosData, store.busquedaServicio, store.filtroCodserServicio);
            $('#contenedor_servicios').css('display', 'flex');
        } else {
            Swal.fire({
                title: 'Error',
                text: response.message || 'No se pudieron cargar los servicios',
                icon: 'error',
                showConfirmButton: false,
                timer: 5000
            });
        }
    }).fail(function () {
        ocultarLoader('loader_servicios');
        Swal.fire({
            title: 'Error',
            text: 'Error de conexion al cargar servicios',
            icon: 'error',
            showConfirmButton: false,
            timer: 5000
        });
    });
}

export function seleccionarServicio(srv) {
    var codben = $('#hid_codben').val();

    if (!codben || !store.beneficiarioSeleccionado) {
        Swal.fire({
            title: 'Atención',
            text: 'Debe seleccionar un beneficiario antes de elegir un servicio.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    store.servicioSeleccionado = srv;
    var cardKey = srv.codser + '|' + srv.numero;

    $('.servicio-card').removeClass('servicio-card--selected');
    $('.servicio-card[data-key="' + cardKey + '"]').addClass('servicio-card--selected');

    $('#detalle_tarifa').hide();
    $('#error_tarifa').hide();

    validarTarifa(srv.codser, srv.numero);
}
