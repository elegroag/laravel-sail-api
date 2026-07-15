import { $App } from '@/App';
import { addFiltro } from '@/Cajas/Glob/Glob';
import { Messages } from '@/Utils';

window.App = $App;

const controller = () => window.ServerController ?? 'admservicios';

const readFiltro = () => ({
    campo: $("input[type='hidden'][name='mcampo-filtro[]']").serialize(),
    condi: $("input[type='hidden'][name='mcondi-filtro[]']").serialize(),
    value: $("input[type='hidden'][name='mvalue-filtro[]']").serialize(),
});

const renderResultado = (response) => {
    if (response) {
        $('#consulta').html(response.consulta);
        $('#paginate').html(response.paginate);
    } else {
        Messages.display('No se pudo cargar la consulta.', 'error');
    }
};

const aplicarFiltro = () => {
    let cantidad = $('#cantidad_paginate').val();
    if (cantidad === null || cantidad === '' || cantidad === undefined) cantidad = 10;

    window.App.trigger('syncro', {
        url: window.App.url(controller() + '/aplicar-filtro'),
        data: {
            ...readFiltro(),
            numero: cantidad,
            estado: $('#chip_estado').val(),
        },
        callback: renderResultado,
    });
};

const buscar = (elem = undefined) => {
    const numero = $('#cantidad_paginate').val();
    let pagina = 1;
    if (elem) {
        pagina = parseInt($(elem).find('a').html());
        if (_.isNaN(pagina) == true) pagina = parseInt($(elem).attr('pagina'));
    }
    if (pagina === 0 || _.isNaN(pagina)) return;

    window.App.trigger('syncro', {
        url: window.App.url(controller() + '/buscar'),
        data: {
            ...readFiltro(),
            pagina: pagina,
            numero: numero,
            estado: $('#chip_estado').val(),
        },
        callback: renderResultado,
    });
};

$(() => {
    window.App.initialize();

    const filtrarModalEl = document.getElementById('filtrar-modal');
    const modalFilter = filtrarModalEl ? new bootstrap.Modal(filtrarModalEl) : null;

    aplicarFiltro();

    // Chips de estado (Todos / Pendientes / Pagadas / Desestimadas / Rechazadas)
    $(document).on('click', "[data-toggle='chip-estado']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        $("[data-toggle='chip-estado']").removeClass('active');
        target.addClass('active');
        $('#chip_estado').val(target.attr('data-estado'));
        aplicarFiltro();
    });

    // Paginación
    $(document).on('click', "[data-toggle='paginate-buscar']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        if (target.hasClass('disabled')) return;
        buscar(target);
    });

    $(document).on('change', "[data-toggle='paginate-change']", () => {
        const cantidad = $('#cantidad_paginate').val();
        if (cantidad == '' || !cantidad) return;
        aplicarFiltro();
    });

    // Modal de filtros
    $(document).on('click', "[data-toggle='header-filtrar']", (e) => {
        e.preventDefault();
        if (modalFilter) modalFilter.show();
    });

    $(document).on('click', "[data-toggle='filter-add']", addFiltro);
    $(document).on('click', "[data-toggle='filter-aplicate']", aplicarFiltro);

    $(document).on('click', "[data-toggle='filter-item-remove']", (e) => {
        e.preventDefault();
        $(e.currentTarget).parent().parent().remove();
        aplicarFiltro();
    });

    $(document).on('click', "[data-toggle='filter-remove']", (e) => {
        e.preventDefault();
        $('#filtro_add').find('tbody').html('');
        aplicarFiltro();
    });

    // Exportar CSV respetando el chip de estado activo
    $(document).on('click', "[data-toggle='reporte-csv']", (e) => {
        e.preventDefault();
        const estado = $('#chip_estado').val();
        let url = window.App.url(controller() + '/reporte/csv');
        if (estado) url += '?estado=' + encodeURIComponent(estado);
        window.location.href = url;
    });
});
