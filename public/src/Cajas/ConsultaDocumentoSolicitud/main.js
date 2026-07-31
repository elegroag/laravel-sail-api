import { $App } from '@/App';

window.App = $App;
window.App.initialize();

let consultando = false;

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
        title: 'Consultando solicitudes…',
        html: 'Buscando el documento en todas las tablas de afiliación.',
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

const estadoClass = (estado) => {
    const code = String(estado || 'x').toLowerCase();
    return `cds-estado cds-estado--${code}`;
};

const renderSolicitud = (item) => `
    <article class="cds-solicitud">
        <div class="cds-solicitud__top">
            <div>
                <p class="cds-solicitud__nombre">${escapeHtml(item.nombre || 'Sin nombre')}</p>
                <p class="cds-solicitud__ruuid">Radicado: ${escapeHtml(item.ruuid || '—')} · ID ${escapeHtml(item.id)}</p>
            </div>
            <span class="${estadoClass(item.estado)}">${escapeHtml(item.estado_detalle || item.estado || 'Sin estado')}</span>
        </div>
        <div class="cds-solicitud__fechas">
            <div><i class="far fa-calendar-alt" aria-hidden="true"></i> Solicitud: ${escapeHtml(item.fecsol || '—')}</div>
            <div><i class="far fa-clock" aria-hidden="true"></i> Sistema: ${escapeHtml(item.fecsis || '—')}</div>
        </div>
    </article>
`;

const renderGrupo = (grupo) => {
    const solicitudes = (grupo.solicitudes || []).map(renderSolicitud).join('');

    return `
        <section class="cds-tipo-card">
            <header class="cds-tipo-card__header">
                <div>
                    <h3 class="cds-tipo-card__title">${escapeHtml(grupo.label)}</h3>
                    <p class="cds-tipo-card__meta">${escapeHtml(grupo.tabla)}.${escapeHtml(grupo.campo)}</p>
                </div>
                <span class="cds-tipo-card__badge">${escapeHtml(grupo.total)} solicitud(es)</span>
            </header>
            <div class="cds-tipo-card__body">
                ${solicitudes}
            </div>
        </section>
    `;
};

const renderResultados = (payload) => {
    const $results = $('#cds-results');
    const $groups = $('#cds-groups');
    const $empty = $('#cds-empty');
    const $summary = $('#cds-summary');

    $groups.empty();
    $results.addClass('is-visible');

    const grupos = payload.grupos || [];
    const total = Number(payload.total || 0);

    if (!grupos.length || total === 0) {
        $summary.html(`Documento <strong>${escapeHtml(payload.documento)}</strong>: sin coincidencias.`);
        $empty.show();
        return;
    }

    $empty.hide();
    $summary.html(
        `Documento <strong>${escapeHtml(payload.documento)}</strong>: ` +
            `<strong>${escapeHtml(total)}</strong> solicitud(es) en ` +
            `<strong>${escapeHtml(grupos.length)}</strong> tipo(s).`
    );
    $groups.html(grupos.map(renderGrupo).join(''));
};

const limpiar = () => {
    $('#form-cds')[0].reset();
    $('#cds-groups').empty();
    $('#cds-summary').empty();
    $('#cds-empty').hide();
    $('#cds-results').removeClass('is-visible');
    $('#documento').focus();
};

const consultar = async () => {
    if (consultando) {
        return;
    }

    const documento = $('#documento').val()?.trim() || '';
    if (!documento) {
        notify('warning', 'Indique el documento de identificación.');
        $('#documento').focus();
        return;
    }

    const url = window.ConsultaDocumentoSolicitudRoutes?.consultar;
    if (!url) {
        notify('error', 'No está configurada la ruta de consulta.');
        return;
    }

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
            body: JSON.stringify({ documento }),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const message =
                data.message ||
                data.errors?.documento?.[0] ||
                'No fue posible completar la consulta.';
            notify('error', message);
            return;
        }

        renderResultados(data);

        if (!data.total) {
            notify('info', 'No se encontraron solicitudes para el documento indicado.');
        } else {
            notify('success', `Se encontraron ${data.total} solicitud(es).`);
        }
    } catch (error) {
        notify('error', error?.message || 'Error de red al consultar.');
    } finally {
        closeLoading();
        setConsultando(false);
    }
};

$(document).ready(() => {
    $('#form-cds').on('submit', (e) => {
        e.preventDefault();
        consultar();
    });

    $('#btn-limpiar').on('click', limpiar);
    $('#documento').focus();
});
