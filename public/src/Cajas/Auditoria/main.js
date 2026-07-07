import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import { $App } from '@/App';

window.App = $App;
let validator;

const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            tipopc: {
                required: true,
            },
            fecini: {
                required: true,
            },
            fecfin: {
                required: true,
            },
        },
    });
};

const getAuditoriaHeaders = (hasExtra) => {
    const headers = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Fecsol', 'Fecapr', 'Radicado', 'Días'];
    if (hasExtra) {
        headers.push('Extra');
    }
    headers.push('Estado');
    headers.push('Acciones');

    return headers;
};

const destroyAuditoriaDataTable = () => {
    const $tbl = $('#tabla-auditoria');
    if ($.fn.DataTable && $tbl.length && $.fn.DataTable.isDataTable($tbl)) {
        $tbl.DataTable().destroy();
    }
};

const initAuditoriaDataTable = () => {
    const $tbl = $('#tabla-auditoria');
    if ($tbl.length === 0 || !$.fn.DataTable) {
        return;
    }

    if ($.fn.DataTable.isDataTable($tbl)) {
        $tbl.DataTable().destroy();
    }

    const actionsColumnIndex = $tbl.find('thead th').length - 1;

    $tbl.DataTable({
        responsive: true,
        autoWidth: false,
        searching: true,
        paging: true,
        lengthChange: true,
        pageLength: 25,
        ordering: true,
        order: [],
        columnDefs: [
            {
                targets: actionsColumnIndex,
                orderable: false,
                searchable: false,
            },
        ],
        language: {
            url: typeof window.DATATABLES_LANG_URL !== 'undefined' ? window.DATATABLES_LANG_URL : undefined,
            decimal: ',',
            thousands: '.',
            processing: 'Procesando...',
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ registros',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros en total)',
            loadingRecords: 'Cargando...',
            zeroRecords: 'No se encontraron resultados',
            emptyTable: 'No hay datos disponibles',
            paginate: {
                first: '<<',
                previous: '<',
                next: '>',
                last: '>>',
            },
        },
    });
};

const parseFecha = (valor) => {
    const [anio, mes, dia] = String(valor).split('-').map(Number);
    return new Date(anio, mes - 1, dia);
};

const rangoSuperaTresMeses = (fecini, fecfin) => {
    const inicio = parseFecha(fecini);
    const fin = parseFecha(fecfin);

    if (Number.isNaN(inicio.getTime()) || Number.isNaN(fin.getTime())) {
        return false;
    }

    const limite = new Date(inicio);
    limite.setMonth(limite.getMonth() + 3);

    return fin > limite;
};

const confirmarRangoAmplio = () => Swal.fire({
    title: 'Rango de fechas amplio',
    html: 'El rango seleccionado supera <strong>3 meses</strong>. Por el volumen de datos, la consulta puede tardar más de lo esperado.<br><br>¿Desea continuar?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonClass: 'btn btn-success btn-fill',
    cancelButtonClass: 'btn btn-danger btn-fill',
    confirmButtonText: 'Sí, continuar',
    cancelButtonText: 'Cancelar',
    allowOutsideClick: false,
}).then((result) => result.value === true);

const validarRangoAntesDeContinuar = (onConfirm) => {
    const fecini = $('#fecini').val();
    const fecfin = $('#fecfin').val();

    if (!rangoSuperaTresMeses(fecini, fecfin)) {
        onConfirm();
        return;
    }

    confirmarRangoAmplio().then((confirmed) => {
        if (confirmed) {
            onConfirm();
        }
    });
};

const buildAuditoriaTable = (data, hasExtra) => {
    const headers = getAuditoriaHeaders(hasExtra);

    let thead = '<thead><tr>';
    for (const h of headers) {
        thead += `<th>${h}</th>`;
    }
    thead += '</tr></thead>';

    const tipopc = $('#tipopc').val();

    const rows = data.map((item) => {
        const cells = [
            item.documento,
            item.nombre,
            item.responsable,
            item.fecha,
            item.fecsol,
            item.fecapr,
            item.radicado,
            item.dias_vencidos,
        ];

        if (hasExtra) {
            cells.push(item.extra ?? '');
        }

        cells.push(item.estado);
        cells.push(`
            <button type="button"
                class="btn btn-info btn-sm btn-detail"
                data-toggle="audit-detail"
                data-id="${item.id}"
                data-tipopc="${tipopc}">
                <i class="fa fa-eye"></i> Ver detalle
            </button>`);

        return `<tr>${cells.map((c) => `<td>${c ?? ''}</td>`).join('')}</tr>`;
    }).join('');

    const csrfToken = document.querySelector("[name='csrf-token']")
        ? document.querySelector("[name='csrf-token']").getAttribute('content')
        : '';

    const exportUrl = $('#consulta').data('export-url') || `${window.ServerController}/exportar`;

    const exportForm = `
<form id="form_exportar_auditoria" action="${exportUrl}" method="POST" target="_blank" class="d-inline-block">
    <input type="hidden" name="_token" value="${csrfToken}">
    <input type="hidden" name="tipopc" value="${$('#tipopc').val()}">
    <input type="hidden" name="fecini" value="${$('#fecini').val()}">
    <input type="hidden" name="fecfin" value="${$('#fecfin').val()}">
    <input type="hidden" name="format" value="xlsx">
    <button type="submit" class="btn btn-success btn-sm">
        <i class="fa fa-file-excel-o"></i> Exportar Excel
    </button>
</form>`;

    const toolbar = `
<div class="d-flex justify-content-end mt-2 mb-2">
    ${exportForm}
</div>`;

    return `<div class="table-responsive mt-2">
${toolbar}
<table class="table table-striped table-bordered datatable-auditoria w-100" id="tabla-auditoria">
${thead}
<tbody>${rows}</tbody>
</table>
</div>`;
};

const ejecutarConsultaAuditoria = () => {
    destroyAuditoriaDataTable();

    window.App.trigger('ajax', {
        url: `${window.ServerController}/consulta`,
        data: {
            tipopc: $('#tipopc').val(),
            fecini: $('#fecini').val(),
            fecfin: $('#fecfin').val(),
        },
        callback: (response) => {
            if (response && Array.isArray(response.data)) {
                if (response.data.length === 0) {
                    $('#consulta').html('<div class="alert alert-info mt-2">No se encontraron resultados</div>');
                    return;
                }

                $('#consulta').html(buildAuditoriaTable(response.data, response.hasExtra === true));
                initAuditoriaDataTable();
                return;
            }

            $('#consulta').html('<div class="alert alert-info mt-2">No se encontraron resultados</div>');
        },
        error: (jqXHR, textStatus) => {
            alert(`Request failed: ${textStatus}`);
        },
    });
};

const consulta_auditoria = () => {
    validatorInit();
    if (!$('#form').valid()) {
        return;
    }

    validarRangoAntesDeContinuar(ejecutarConsultaAuditoria);
};

const reporte_auditoria = () => {
    validatorInit();
    if (!$('#form').valid()) {
        return;
    }

    validarRangoAntesDeContinuar(() => {
        $('#form').submit();
    });
};

$(() => {
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
        locale: Spanish,
        allowInput: true,
        disableTouchKeyboard: true,
        altInput: true,
        altFormat: 'd/m/Y',
    });

    window.App.initialize();
    const auditDetailModal = new bootstrap.Modal(document.getElementById('auditDetailModal'));

    $(document).on('click', "[data-toggle='consulta']", (e) => {
        e.preventDefault();
        consulta_auditoria();
    });

    $(document).on('click', "[data-toggle='reporte']", (e) => {
        e.preventDefault();
        reporte_auditoria();
    });

    $(document).on('click', '[data-toggle="audit-detail"]', (e) => {
        e.preventDefault();
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        const tipopc = btn.dataset.tipopc;

        $('#auditDetailModalbody').html('<div class="p-4 text-center text-muted">Cargando...</div>');
        auditDetailModal.show();

        window.App.trigger('ajax', {
            url: `${window.ServerController}/infor`,
            data: { tipopc, id },
            callback: (response) => {
                if (response && response.success === true) {
                    $('#auditDetailModalbody').html(response.html || '<div class="alert alert-info m-3">Sin información disponible.</div>');
                } else {
                    $('#auditDetailModalbody').html(
                        `<div class="alert alert-danger m-3">${response?.msj || 'No fue posible cargar el detalle.'}</div>`
                    );
                }
            },
            error: () => {
                $('#auditDetailModalbody').html(
                    '<div class="alert alert-danger m-3">No fue posible cargar el detalle.</div>'
                );
            },
        });
    });
});
