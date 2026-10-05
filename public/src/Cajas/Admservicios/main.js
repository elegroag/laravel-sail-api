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

$(() => {
    window.App.initialize();

    const filtrarModalEl = document.getElementById('filtrar-modal');
    const modalFilter = filtrarModalEl ? new bootstrap.Modal(filtrarModalEl) : null;

    if ($('#consulta').length) {
        aplicarFiltro();
    }

    // Chips de estado (Todos / Pendientes / Pagadas / Desestimadas / Rechazadas)
    $(document).on('click', "[data-toggle='chip-estado']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        $("[data-toggle='chip-estado']").removeClass('active');
        target.addClass('active');
        $('#chip_estado').val(target.attr('data-estado'));
        aplicarFiltro();
    });

    // Doble clic en la fila: mismo efecto que el botón de ver detalle
    $(document).on('dblclick', '#consulta tbody tr[data-href]', (e) => {
        if ($(e.target).closest('a, button').length) return;
        const href = $(e.currentTarget).data('href');
        if (!href) return;
        window.open(href, '_blank', 'noopener');
    });

    // Registrar en Subsidio: validar ePayco + guardar-venta
    $(document).on('click', "[data-toggle='registrar-subsidio']", (e) => {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const id = $btn.data('id');
        if (!id) return;

        const $resultado = $('#resultado_validacion_epayco');
        $resultado.html('<div class="text-muted small">Validando pago ePayco y registrando en Subsidio…</div>');
        $btn.prop('disabled', true);

        window.App.trigger('syncro', {
            url: window.App.url(controller() + '/registrar-subsidio/' + id),
            data: {},
            callback: (response) => {
                $btn.prop('disabled', false);
                if (!response) {
                    $resultado.html('<div class="alert alert-danger mb-0 py-2">Sin respuesta del servidor.</div>');
                    return;
                }

                const pago = response.pago || null;
                const registrada = !!(response.registrada);
                const alertClass = response.success
                    ? (registrada ? 'alert-success' : 'alert-warning')
                    : 'alert-danger';

                let detalle = '';
                if (pago) {
                    detalle =
                        '<ul class="mb-0 small mt-1">' +
                        '<li><strong>ref_payco:</strong> <code>' + (pago.ref_payco || '—') + '</code></li>' +
                        '<li><strong>Estado ePayco:</strong> ' + (pago.cod_estado ?? '—') + ' — ' + (pago.respuesta || '—') + '</li>' +
                        '<li><strong>Motivo:</strong> ' + (pago.motivo || '—') + '</li>' +
                        '<li><strong>Monto:</strong> ' + (pago.monto ?? '—') + '</li>' +
                        '<li><strong>transaction_id:</strong> <code>' + (pago.transaction_id || '—') + '</code></li>' +
                        '<li><strong>approval_code:</strong> <code>' + (pago.approval_code || '—') + '</code></li>' +
                        '</ul>';
                }

                if (response.success && response.html) {
                    $('#detalle_precompra').html(response.html);
                    Messages.display(response.msj || 'Registro en Subsidio completado.', 'success');
                    return;
                }

                $resultado.html(
                    '<div class="alert ' + alertClass + ' mb-0 py-2">' +
                        '<div>' + (response.msj || 'Proceso finalizado.') + '</div>' +
                        detalle +
                    '</div>'
                );

                if (!response.success) {
                    Messages.display(response.msj || 'No se pudo registrar en Subsidio.', 'error');
                }
            },
        });
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
