import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';
import { Messages } from '@/Utils';

window.App = $App;

const paramsConciliacion = (pagina = 1) => ({
    fecha_inicio: $('#fecha_inicio').val(),
    fecha_fin: $('#fecha_fin').val(),
    estado: $('#filtro_estado').val() || '',
    pagina: pagina,
    numero: $('#cantidad_paginate').val() || 15,
});

const renderResultado = (response) => {
    if (!response) {
        Messages.display('No se pudo cargar la conciliación.', 'error');
        return;
    }

    if (response.success === false && response.message) {
        Messages.display(response.message, 'error');
    }

    if (response.consulta !== undefined) {
        $('#consulta').html(response.consulta);
    }
    if (response.paginate !== undefined) {
        $('#paginate').html(response.paginate);
    }
    if (response.resumen !== undefined) {
        $('#resumen').html(response.resumen);
    }

    const total = response.total_filas;
    if (typeof total === 'number') {
        $('#mensaje').html(
            '<div class="small text-muted">Filas totales (filtro actual): <strong>' + total + '</strong></div>'
        );
    }
};

const conciliar = (elem = undefined) => {
    let pagina = 1;
    if (elem) {
        pagina = parseInt($(elem).find('a').html(), 10);
        if (Number.isNaN(pagina)) {
            pagina = parseInt($(elem).attr('pagina'), 10);
        }
    }
    if (!pagina || Number.isNaN(pagina)) pagina = 1;

    const data = paramsConciliacion(pagina);
    if (!data.fecha_inicio || !data.fecha_fin) {
        Messages.display('Indique fecha inicio y fecha fin.', 'warning');
        return;
    }

    $('#btn_conciliar').prop('disabled', true);
    if (pagina === 1 && !elem) {
        $('#consulta').html('<div class="text-muted small">Conciliando…</div>');
        $('#paginate').html('');
    }

    window.App.trigger('syncro', {
        url: window.routesConciliacion.buscar,
        data: data,
        callback: (response) => {
            $('#btn_conciliar').prop('disabled', false);
            renderResultado(response);
        },
    });
};

const exportarCsv = (e) => {
    e.preventDefault();
    const data = paramsConciliacion(1);
    if (!data.fecha_inicio || !data.fecha_fin) {
        Messages.display('Indique fecha inicio y fecha fin.', 'warning');
        return;
    }

    const qs = $.param({
        fecha_inicio: data.fecha_inicio,
        fecha_fin: data.fecha_fin,
        estado: data.estado,
    });
    window.location.href = window.routesConciliacion.reporte + '?' + qs;
};

$(() => {
    window.App.initialize();

    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
    });

    $('#form_conciliacion').on('submit', (e) => {
        e.preventDefault();
        conciliar();
    });

    $('#btn_exportar_csv').on('click', exportarCsv);

    $(document).on('click', "[data-toggle='paginate-buscar']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        if (target.hasClass('disabled')) return;
        conciliar(target);
    });

    $(document).on('change', "[data-toggle='paginate-change']", () => {
        conciliar();
    });

    $(document).on('click', "[data-toggle='registrar-subsidio-conciliacion']", (e) => {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const id = $btn.data('id');
        if (!id) return;

        $btn.prop('disabled', true).text('Registrando…');

        window.App.trigger('syncro', {
            url: window.routesConciliacion.registrarSubsidioBase + '/' + id,
            data: {},
            callback: (response) => {
                $btn.prop('disabled', false).text('Registrar en Subsidio');
                if (!response) {
                    Messages.display('Sin respuesta del servidor.', 'error');
                    return;
                }

                const ok = !!(response.success);
                const msg = response.msj || response.message || (ok ? 'Registrado' : 'No se pudo registrar');
                Messages.display(msg, ok ? 'success' : 'error');

                if (ok && response.registrada) {
                    conciliar();
                }
            },
        });
    });
});
