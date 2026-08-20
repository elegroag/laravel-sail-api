/**
 * Filtros del catalogo de servicios: busqueda por texto, filtro por codser y
 * el select2 asociado.
 *
 * Depende de globales del layout: $ (jQuery), $.fn.select2.
 */
import store from './store.js';

export function servicioCoincideBusqueda(srv, query) {
    if (!query) return true;
    var texto = (
        (srv.nombre || '') + ' ' +
        (srv.detalle || '') + ' ' +
        (srv.codser || '') + ' ' +
        (srv.uis_ciudad || '')
    ).toLowerCase();
    return texto.indexOf(query.toLowerCase()) !== -1;
}

export function servicioCoincideCodser(srv, codser) {
    if (!codser) return true;
    return String(srv.codser) === String(codser);
}

export function servicioPasaFiltros(srv, query, codser) {
    return servicioCoincideBusqueda(srv, query) && servicioCoincideCodser(srv, codser);
}

export function initFiltroCodserSelect2() {
    var $select = $('#filtro_codser');

    if (!$select.length || typeof $.fn.select2 === 'undefined') {
        return;
    }

    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }

    $select.select2({
        allowClear: true,
        placeholder: 'Todos los servicios',
        width: '100%',
        minimumResultsForSearch: 6,
        language: {
            noResults: function () {
                return 'Sin resultados';
            },
            searching: function () {
                return 'Buscando...';
            }
        }
    });
}

export function poblarFiltroCodser(servicios) {
    var select = $('#filtro_codser');
    var valorActual = store.filtroCodserServicio;
    var opciones = {};

    if (select.hasClass('select2-hidden-accessible')) {
        select.select2('destroy');
    }

    $.each(servicios || [], function (i, srv) {
        if (!srv.codser || opciones[srv.codser]) {
            return;
        }
        opciones[srv.codser] = srv.detalle || srv.nombre || String(srv.codser);
    });

    var items = Object.keys(opciones).map(function (codser) {
        return { codser: codser, detalle: opciones[codser] };
    });

    items.sort(function (a, b) {
        return String(a.detalle).localeCompare(String(b.detalle), 'es', { sensitivity: 'base' });
    });

    select.find('option:not(:first)').remove();

    $.each(items, function (i, item) {
        select.append(
            $('<option></option>')
                .val(item.codser)
                .text(item.detalle)
        );
    });

    if (valorActual && opciones[valorActual]) {
        select.val(valorActual);
    } else {
        store.filtroCodserServicio = '';
        select.val('');
    }

    initFiltroCodserSelect2();
}
