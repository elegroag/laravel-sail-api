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

const escapeHtml = (value) =>
    String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

const formatMoney = (value) => {
    const number = Number(value || 0);
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(number);
};

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

const showLoading = () => {
    Swal.fire({
        title: 'Consultando ventas en línea…',
        html: 'Espere un momento mientras se consulta el reporte.',
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

const buildPayload = ({ page = 1 } = {}) => {
    const payload = {
        fecini: $('#fecini').val() || '',
        fecfin: $('#fecfin').val() || '',
        page,
        per_page: PER_PAGE,
    };

    const estado = $('#estado').val();
    if (estado) {
        payload.estado = estado;
    }

    return payload;
};

const validateClient = (payload) => {
    if (!payload.fecini || !payload.fecfin) {
        return 'Indique el rango de fechas.';
    }

    return null;
};

const renderRows = (rows) => {
    const $tbody = $('#tabla-rcs tbody');
    $tbody.empty();

    if (!rows.length) {
        $tbody.append(
            '<tr><td colspan="10" class="text-center text-muted py-3">Sin resultados para los filtros indicados.</td></tr>'
        );
        return;
    }

    rows.forEach((row) => {
        const estadoCode = String(row.estado || '').toLowerCase();
        $tbody.append(`
            <tr>
                <td>${escapeHtml(row.id)}</td>
                <td>${escapeHtml(row.documento)}</td>
                <td>${escapeHtml(row.codben || '—')}</td>
                <td>${escapeHtml(row.codser)}</td>
                <td>${escapeHtml(row.numero)}</td>
                <td>${escapeHtml(row.valor != null ? formatMoney(row.valor) : '—')}</td>
                <td><span class="rcs-estado rcs-estado--${estadoCode}">${escapeHtml(row.estado_detalle || row.estado)}</span></td>
                <td>${escapeHtml(row.ref_payco || '—')}</td>
                <td>${escapeHtml(row.fecha_precompra || '—')}</td>
                <td>${escapeHtml(row.fecha_pago || '—')}</td>
            </tr>
        `);
    });
};

const renderResumen = (resumen) => {
    const $wrap = $('#rcs-resumen');
    $wrap.empty();

    const porEstado = resumen?.por_estado || {};
    const labels = window.ReporteComprasServiciosEstados || {};

    Object.keys(porEstado).forEach((codigo) => {
        const count = Number(porEstado[codigo] || 0);
        if (!count) {
            return;
        }
        const label = labels[codigo] || codigo;
        $wrap.append(
            `<span class="rcs-chip rcs-chip--${String(codigo).toLowerCase()}">${escapeHtml(codigo)} ${escapeHtml(label)}: ${count}</span>`
        );
    });

    $('[data-role="valor-total"]').text(formatMoney(resumen?.valor_total || 0));
    $('[data-role="valor-pagado"]').text(formatMoney(resumen?.valor_pagado || 0));
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
    const $wrap = $('#rcs-pagination');
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

    $('[data-role="total"]').text(String(total));
    $('[data-role="rango"]').text(`(${payload.fecini} a ${payload.fecfin})`);

    renderResumen(data.resumen || {});
    renderRows(rows);
    renderPagination(data.pagination || null);
    $('#rcs-results').addClass('is-visible');
    setImprimirEnabled(total > 0);

    if (!notifyResult) {
        return;
    }

    if (!total) {
        notify('info', 'No se encontraron ventas en línea para los filtros indicados.');
    } else {
        notify('success', `Se encontraron ${total} venta(s) en línea.`);
    }
};

const consultar = async ({ page = 1, notifyResult = true } = {}) => {
    if (consultando) {
        return;
    }

    const payload = buildPayload({ page });
    const validationError = validateClient(payload);
    if (validationError) {
        notify('warning', validationError);
        return;
    }

    const url = window.ReporteComprasServiciosRoutes?.consultar;
    if (!url) {
        notify('error', 'No está configurada la ruta de consulta.');
        return;
    }

    lastFilters = { ...payload };
    setConsultando(true);
    showLoading();

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const message =
                data.message ||
                Object.values(data.errors || {})
                    .flat()
                    .join(' ') ||
                'No fue posible completar la consulta.';
            notify('error', message);
            return;
        }

        applyResult(data, payload, { notifyResult });
    } catch (error) {
        notify('error', error?.message || 'Error de red al consultar.');
    } finally {
        closeLoading();
        setConsultando(false);
    }
};

$(document).ready(() => {
    flatpickr('.datepicker', {
        locale: Spanish,
        dateFormat: 'Y-m-d',
        allowInput: true,
    });

    $('#form-rcs').on('submit', (e) => {
        e.preventDefault();
        consultar({ page: 1 });
    });

    $('#btn-imprimir').on('click', () => {
        window.print();
    });

    $('#rcs-pagination').on('click', 'button[data-page]', (e) => {
        const page = Number($(e.currentTarget).attr('data-page') || 1);
        if (!page || !lastFilters) {
            return;
        }
        if (currentPagination && page === currentPagination.page) {
            return;
        }
        consultar({ page, notifyResult: false });
    });
});
