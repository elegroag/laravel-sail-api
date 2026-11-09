import { $App } from '@/App';
import { Messages } from '@/Utils';

window.App = $App;

const routes = () => window.routesDevolucionServicios || {};

const paramsBuscar = () => ({
    estado: $('#chip_estado').val() || '',
    documento: $('#filtro_documento').val() || '',
    q: $('#filtro_q').val() || '',
    numero: $('#cantidad_paginate').val() || 15,
});

const actualizarContadores = (resumen) => {
    if (!resumen || typeof resumen !== 'object') return;
    Object.keys(resumen).forEach((key) => {
        $("[data-count='" + key + "']").text(String(resumen[key] ?? 0));
    });
};

const renderResultado = (response) => {
    if (!response) {
        Messages.display('No se pudo cargar la bandeja de devoluciones.', 'error');
        return;
    }

    if (response.consulta !== undefined) {
        $('#consulta').html(response.consulta);
    }
    if (response.paginate !== undefined) {
        $('#paginate').html(response.paginate);
    }
    if (response.resumen) {
        actualizarContadores(response.resumen);
    }
    if (typeof response.total === 'number') {
        $('#mensaje').html(
            '<div class="small text-muted">Registros: <strong>' + response.total + '</strong></div>'
        );
    }
};

const buscar = (elem = undefined) => {
    let pagina = 1;
    if (elem) {
        pagina = parseInt($(elem).find('a').html(), 10);
        if (Number.isNaN(pagina)) {
            pagina = parseInt($(elem).attr('pagina'), 10);
        }
    }
    if (!pagina || Number.isNaN(pagina)) pagina = 1;

    window.App.trigger('syncro', {
        url: routes().buscar,
        data: {
            ...paramsBuscar(),
            pagina: pagina,
        },
        callback: renderResultado,
    });
};

const abrirModalGestion = (accion, id, resumen) => {
    $('#hid_devolucion_id').val(String(id));
    $('#hid_devolucion_accion').val(String(accion));
    $('#modal_gestion_resumen').text(resumen || '');
    $('#txt_nota_gestion').val('');

    const titulos = {
        'en-tramite': 'Poner en trámite',
        aprobar: 'Aprobar devolución',
        rechazar: 'Rechazar devolución',
    };
    $('#modal_gestion_titulo').text(titulos[accion] || 'Gestionar devolución');

    const $btn = $('#btn_confirmar_gestion');
    $btn.removeClass('btn-primary btn-success btn-danger');
    if (accion === 'aprobar') $btn.addClass('btn-success');
    else if (accion === 'rechazar') $btn.addClass('btn-danger');
    else $btn.addClass('btn-primary');

    const el = document.getElementById('modal_gestion_devolucion');
    if (el && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(el).show();
    }
};

const confirmarGestion = () => {
    const id = $('#hid_devolucion_id').val();
    const accion = $('#hid_devolucion_accion').val();
    if (!id || !accion) return;

    const base = routes().enTramiteBase;
    const url = base + '/' + id + '/' + accion;

    window.App.trigger('syncro', {
        url: url,
        data: {
            nota: $('#txt_nota_gestion').val() || '',
        },
        callback: (response) => {
            if (!response) {
                Messages.display('Sin respuesta del servidor.', 'error');
                return;
            }
            if (response.success) {
                Messages.display(response.msj || 'Operación completada.', 'success');
                const el = document.getElementById('modal_gestion_devolucion');
                if (el && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(el).hide();
                }
                buscar();
                return;
            }
            Messages.display(response.msj || 'No se pudo completar la operación.', 'error');
        },
    });
};

$(() => {
    window.App.initialize();
    buscar();

    $(document).on('click', "[data-toggle='chip-estado']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        $("[data-toggle='chip-estado']").removeClass('active');
        target.addClass('active');
        $('#chip_estado').val(target.attr('data-estado') || '');
        buscar();
    });

    $('#form_devoluciones').on('submit', (e) => {
        e.preventDefault();
        buscar();
    });

    $(document).on('click', "[data-toggle='paginate-buscar']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        if (target.hasClass('disabled')) return;
        buscar(target);
    });

    $(document).on('change', "[data-toggle='paginate-change']", () => {
        buscar();
    });

    $(document).on('click', "[data-toggle='dev-accion']", (e) => {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        abrirModalGestion($btn.data('accion'), $btn.data('id'), $btn.data('resumen'));
    });

    $(document).on('click', '#btn_confirmar_gestion', (e) => {
        e.preventDefault();
        confirmarGestion();
    });
});
