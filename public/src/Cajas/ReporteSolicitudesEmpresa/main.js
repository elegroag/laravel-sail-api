import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';

window.App = $App;
window.App.initialize();

let consultando = false;
let lastFilters = null;
let currentPagination = null;

const PER_PAGE = 25;

const getCsrfToken = () => {
    const meta = document.querySelector("[name='csrf-token']");
    return meta ? meta.getAttribute('content') : $('input[name="_token"]').val();
};

const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

const notify = (type, message, title = null) => {
    const titles = {
        success: 'Consulta exitosa',
        error: 'Atención',
        warning: 'Validación',
        info: 'Sin resultados',
    };

    $App.alert(type, {
        title: title || titles[type] || 'Notificación',
        message,
        timer: type === 'error' || type === 'warning' ? 9000 : 4500,
    });
};

const showLoading = (title = 'Consultando solicitudes…', html = 'Espere un momento mientras se consulta el reporte.') => {
    Swal.fire({
        title,
        html,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
};

const closeLoading = () => {
    if (Swal.isVisible()) {
        Swal.close();
    }
};

const setConsultando = (loading) => {
    consultando = loading;
    const $btn = $('#btn-consultar');
    $btn.prop('disabled', loading).attr('aria-busy', loading ? 'true' : 'false');
    $btn.find('[data-role="btn-label"]').text(loading ? 'Consultando…' : 'Consultar');
    $btn.find('i').attr('class', loading ? 'fas fa-spinner fa-spin' : 'fas fa-search');
};

const setImprimirEnabled = (enabled) => {
    $('#btn-imprimir')
        .prop('disabled', !enabled)
        .attr('aria-disabled', enabled ? 'false' : 'true');
};

const selectedTipopcs = () => $('.tipopc-check:checked').map((_, el) => el.value).get();

const buildPayload = ({ page = 1, refresh = false } = {}) => {
    const tipopcs = selectedTipopcs();
    const payload = {
        documento: $('#documento').val()?.trim() || '',
        coddoc: $('#coddoc').val() || '',
        fecini: $('#fecini').val() || '',
        fecfin: $('#fecfin').val() || '',
        tipopcs,
        page,
        per_page: PER_PAGE,
        refresh,
    };

    const estado = $('#estado').val();
    if (estado) {
        payload.estado = estado;
    }

    return payload;
};

const validateClient = (payload) => {
    if (!payload.documento) {
        return 'Indique el documento de la empresa.';
    }
    if (!payload.coddoc) {
        return 'Seleccione el tipo de documento.';
    }
    if (!payload.fecini || !payload.fecfin) {
        return 'Indique el rango de fechas.';
    }
    if (!payload.tipopcs.length) {
        return 'Seleccione al menos un tipo de solicitud.';
    }

    return null;
};

const renderRows = (rows) => {
    const $tbody = $('#tabla-rse tbody');
    $tbody.empty();

    if (!rows.length) {
        $tbody.append('<tr><td colspan="8" class="text-center text-muted py-3">Sin resultados para los filtros indicados.</td></tr>');
        return;
    }

    rows.forEach((row) => {
        $tbody.append(`
            <tr>
                <td>${escapeHtml(row.tipo_label)}</td>
                <td>${escapeHtml(row.ruuid)}</td>
                <td>${escapeHtml(row.documento_afiliado)}</td>
                <td>${escapeHtml(row.nombre)}</td>
                <td>${escapeHtml(row.nit || '—')}</td>
                <td>${escapeHtml(row.fecsol || '—')}</td>
                <td>${escapeHtml(row.estado)}</td>
                <td>${escapeHtml(row.fecha_cierre || '—')}</td>
            </tr>
        `);
    });
};

const pageWindow = (page, lastPage) => {
    const windowSize = 5;
    let start = Math.max(1, page - Math.floor(windowSize / 2));
    let end = Math.min(lastPage, start + windowSize - 1);
    start = Math.max(1, end - windowSize + 1);

    const pages = [];
    for (let i = start; i <= end; i += 1) {
        pages.push(i);
    }

    return pages;
};

const renderPagination = (pagination) => {
    const $wrap = $('#rse-pagination');
    const $info = $('[data-role="page-info"]');
    const $controls = $('[data-role="page-controls"]');

    currentPagination = pagination || null;

    if (!pagination || pagination.total === 0) {
        $wrap.attr('hidden', true);
        $controls.empty();
        $info.empty();
        return;
    }

    const { page, last_page: lastPage, from, to, total } = pagination;
    $info.text(`Mostrando ${from}–${to} de ${total}`);
    $controls.empty();

    if (lastPage <= 1) {
        $wrap.attr('hidden', true);
        return;
    }

    $wrap.removeAttr('hidden');

    const addBtn = (label, targetPage, { disabled = false, current = false, aria = null } = {}) => {
        const classes = ['btn', current ? 'btn-primary is-current' : 'btn-outline-primary'];
        const $btn = $(`<button type="button" class="${classes.join(' ')}"></button>`)
            .text(label)
            .prop('disabled', disabled || current)
            .attr('data-page', targetPage);

        if (aria) {
            $btn.attr('aria-label', aria);
        }
        if (current) {
            $btn.attr('aria-current', 'page');
        }

        $controls.append($btn);
    };

    addBtn('«', Math.max(1, page - 1), {
        disabled: page <= 1,
        aria: 'Página anterior',
    });

    pageWindow(page, lastPage).forEach((p) => {
        addBtn(String(p), p, { current: p === page });
    });

    addBtn('»', Math.min(lastPage, page + 1), {
        disabled: page >= lastPage,
        aria: 'Página siguiente',
    });
};

const applyResult = (data, payload, { notifyResult = true } = {}) => {
    const rows = data.rows || [];
    const total = data.total ?? rows.length;
    const pagination = data.pagination || null;

    $('[data-role="empresa-doc"]').text(`${payload.documento} (coddoc ${payload.coddoc})`);
    $('[data-role="total"]').text(String(total));

    const $hint = $('[data-role="cache-hint"]');
    if (data.from_cache) {
        $hint.text('(desde caché)').removeAttr('hidden');
    } else {
        $hint.attr('hidden', true).text('');
    }

    renderRows(rows);
    renderPagination(pagination);
    $('#rse-results').addClass('is-visible');
    setImprimirEnabled(total > 0);

    if (!notifyResult) {
        return;
    }

    if (total === 0) {
        notify('info', 'La consulta no devolvió solicitudes para los filtros indicados.');
    } else {
        const page = pagination?.page || 1;
        const lastPage = pagination?.last_page || 1;
        notify(
            'success',
            `Se encontraron <strong>${total}</strong> solicitud(es)`
            + (lastPage > 1 ? ` — página ${page} de ${lastPage}.` : '.'),
        );
    }
};

const consultar = async ({ page = 1, refresh = false, notifyResult = true } = {}) => {
    const payload = lastFilters && !refresh
        ? { ...lastFilters, page, refresh: false, per_page: PER_PAGE }
        : buildPayload({ page, refresh });

    const clientError = validateClient(payload);
    if (clientError) {
        notify('warning', clientError);
        return;
    }

    const action = window.ReporteSolicitudesEmpresaRoutes?.consultar;
    if (!action || consultando) {
        return;
    }

    setConsultando(true);
    if (refresh) {
        setImprimirEnabled(false);
        $('#rse-results').removeClass('is-visible');
        showLoading();
    } else {
        showLoading('Cargando página…', 'Obteniendo registros desde caché.');
    }

    try {
        const response = await fetch(action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const message = data.message
                || Object.values(data.errors || {}).flat().join(' ')
                || 'No fue posible consultar el reporte.';
            throw new Error(message);
        }

        lastFilters = {
            documento: payload.documento,
            coddoc: payload.coddoc,
            fecini: payload.fecini,
            fecfin: payload.fecfin,
            tipopcs: payload.tipopcs,
            ...(payload.estado ? { estado: payload.estado } : {}),
        };

        closeLoading();
        applyResult(data, payload, { notifyResult });
    } catch (error) {
        closeLoading();
        notify('error', error.message || 'Error al consultar el reporte.');
    } finally {
        setConsultando(false);
    }
};

$(document).ready(() => {
    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
    });

    setImprimirEnabled(false);

    $('#form-rse').on('submit', (event) => {
        event.preventDefault();
        lastFilters = null;
        consultar({ page: 1, refresh: true, notifyResult: true });
    });

    $(document).on('click', '[data-role="page-controls"] button[data-page]', (event) => {
        event.preventDefault();
        const page = Number($(event.currentTarget).attr('data-page'));
        if (!page || consultando || page === currentPagination?.page) {
            return;
        }
        consultar({ page, refresh: false, notifyResult: false });
    });

    $('#btn-imprimir').on('click', () => {
        if ($('#btn-imprimir').prop('disabled')) {
            notify('warning', 'Primero consulte el reporte para poder imprimirlo.');
            return;
        }
        window.print();
    });
});
