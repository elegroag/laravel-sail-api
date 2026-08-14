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
    const cantidadActual = $('#cantidad_paginate').val();
    if (response) {
        $('#consulta').html(response.consulta);
        $('#paginate').html(response.paginate);
        if (cantidadActual) {
            $('#cantidad_paginate').val(cantidadActual);
        }
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

const abrirDetallePrecompra = (id) => {
    const modalEl = document.getElementById('modal_detalle_precompra');
    if (!modalEl) {
        Messages.display('No se encontró el modal de detalle.', 'error');
        return;
    }

    const $body = $('#modal_detalle_precompra_body');
    const $titulo = $('#modal_detalle_precompra_titulo');
    $titulo.text('Detalle de precompra #' + id);
    $body.html('<div class="text-center text-muted py-4">Cargando…</div>');

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    window.App.trigger('syncro', {
        url: window.App.url(controller() + '/detalle/' + id),
        data: {},
        callback: (response) => {
            if (response && response.success && response.html) {
                if (response.titulo) {
                    $titulo.text(response.titulo);
                }
                $body.html(response.html);
                return;
            }
            $body.html(
                '<div class="alert alert-warning mb-0">' +
                    (response?.msj || 'No se pudo cargar el detalle.') +
                '</div>'
            );
        },
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

    // Detalle precompra + transacciones
    $(document).on('click', "[data-toggle='detalle-precompra']", (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).data('id');
        if (!id) return;
        abrirDetallePrecompra(id);
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
