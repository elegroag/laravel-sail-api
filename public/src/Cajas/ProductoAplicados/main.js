import { $App } from '@/App';
import Logger from '@/Common/Logger';
import { Messages } from '@/Utils';

const logger = new Logger();

window.App = $App;

const controller = () => window.ServerController ?? 'admproductos';

const dataTableConfig = {
    processing: 'Procesando...',
    lengthMenu: 'Mostrar _MENU_ resultados por pagínas',
    zeroRecords: 'No se encontraron resultados',
    info: 'Mostrando pagína _PAGE_ de _PAGES_',
    infoEmpty: 'No records available',
    infoFiltered: '(filtered from _MAX_ total records)',
    emptyTable: 'Ningún dato disponible en esta tabla',
    search: 'Buscar',
    paginate: {
        next: '>>',
        previus: '<<',
        first: 'PR',
        last: 'UL',
        previous: '<<',
    },
    loadingRecords: 'Cargando...',
    buttons: {
        copy: 'Copiar',
        colvis: 'Visibilidad',
        collection: 'Colección',
        colvisRestore: 'Restaurar visibilidad',
        copyKeys:
            'Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.',
        copySuccess: {
            1: 'Copiada 1 fila al portapapeles',
            _: 'Copiadas %d fila al portapapeles',
        },
    },
};

const estadosAplicado = {
    A: 'Activo',
    I: 'Inactivo',
    R: 'Rechazado',
};

let _datatable;

const loadData = (aplicados) => {
    if (_datatable === undefined) {
        _datatable = $('#datatable')
            .DataTable({
                paging: true,
                pageLength: 10,
                pagingType: 'full_numbers',
                info: false,
                columns: [
                    { title: 'Cedtra', data: 'cedtra', width: '10%' },
                    { title: 'Docben', data: 'docben', width: '10%' },
                    { title: 'Estado', data: 'estado', width: '5%' },
                    { title: 'Fecha', data: 'fecha', width: '10%' },
                    { title: 'Pin', data: 'pin' },
                    { title: 'Opciones', data: 'options', width: '20%' },
                ],
                language: dataTableConfig,
            })
            .draw(false);
    } else {
        _datatable.rows().clear().draw();
    }

    if (_.size(aplicados) > 0) {
        const _data = [];
        for (const ai in aplicados) {
            const aplicado = aplicados[ai];
            let _btrechazo = '';
            if (aplicado.estado === 'A') {
                _btrechazo = `<button type="button" toggle='rechazar' data-cid='${aplicado.id}' class="btn btn-sm btn-danger">Rechazar</button>`;
            }
            const estadoLabel = estadosAplicado[aplicado.estado] ?? 'No definida';

            _data[ai] = {
                id: aplicado.id,
                codser: aplicado.codser,
                docben: aplicado.docben,
                estado: estadoLabel,
                fecha: aplicado.fecha,
                cedtra: aplicado.cedtra,
                pin: aplicado.pin,
                options: `<button type="button" toggle='detalle' data-cid='${aplicado.id}' class="btn btn-sm btn-primary">Detalles</button> ${_btrechazo}`,
            };
        }
        _datatable.rows.add(_data).draw();
    }

    $('table').attr('class', 'table table-sm table-bordered');
    $('[type="search"]').addClass('row form-control');
    $('[type="search"]').css('display', 'inline-block');
    $('[type="search"]').css('width', '120px');
};

const loadDetalle = (data) => {
    if (!data?.trabajador) {
        logger.warn('ProductoAplicados:loadDetalle - trabajador no disponible en respuesta', { id: data?.id });
    }

    const template = _.template($('#tmp_detalle_aplicado').html());
    $('#showDetalleAplicado').html(template(data));
};

const buscarLista = () => {
    const codser = $('#codser').val();
    logger.info('ProductoAplicados:buscarLista - solicitando aplicados', { codser });

    window.App.trigger('ajax', {
        url: `${controller()}/buscar_afiliados-aplicados/${codser}`,
        data: {},
        callback: (response) => {
            if (response?.success) {
                logger.info('ProductoAplicados:buscarLista - datos recibidos', {
                    codser,
                    count: response.data?.length ?? 0,
                });
                loadData(response.data ?? []);
                return;
            }

            logger.warn('ProductoAplicados:buscarLista - respuesta sin éxito', response);
            Messages.display(response?.msj ?? 'No se pudo cargar la lista de aplicados.', 'error');
        },
    });
};

const buscarAplicado = (id) => {
    logger.info('ProductoAplicados:buscarAplicado - solicitando detalle', { id });

    window.App.trigger('ajax', {
        url: `${controller()}/detalle-aplicado/${id}`,
        data: {},
        callback: (response) => {
            if (response?.success) {
                logger.info('ProductoAplicados:buscarAplicado - detalle recibido', { id });
                loadDetalle(response.data);
                return;
            }

            logger.warn('ProductoAplicados:buscarAplicado - respuesta sin éxito', response);
            Messages.display(response?.msj ?? 'No se pudo cargar el detalle del aplicado.', 'error');
        },
    });
};

const rechazarAplicado = (id) => {
    const codser = $('#codser').val();
    logger.info('ProductoAplicados:rechazarAplicado - solicitando rechazo', { id, codser });

    window.App.trigger('ajax', {
        url: `${controller()}/rechazar/${id}`,
        data: { codser },
        callback: (response) => {
            if (response?.success) {
                logger.info('ProductoAplicados:rechazarAplicado - aplicado rechazado', { id });
                buscarLista();
                swal.fire({
                    title: 'Notificación Alerta',
                    text: response.msj,
                    icon: 'warning',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 10000,
                });
                return;
            }

            logger.warn('ProductoAplicados:rechazarAplicado - respuesta sin éxito', response);
            swal.fire({
                title: 'Notificación Alerta',
                text: response?.msj ?? 'No fue posible rechazar el aplicado.',
                icon: 'warning',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 10000,
            });
        },
    });
};

$(() => {
    window.App.initialize();

    $.fn.DTbl_columnCount = function () {
        return $('th', $(this).find('thead')).length;
    };

    buscarLista();

    $(document).on('click', "button[toggle='detalle']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        buscarAplicado(target.attr('data-cid'));
    });

    $(document).on('click', "button[toggle='rechazar']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        swal.fire({
            title: '¡Confirmar!',
            html: "<p style='font-size:0.97rem'>¿Está seguro que desea rechazar la solicitud del afiliado.?</p>",
            showCancelButton: true,
            confirmButtonClass: 'btn btn-sm btn-success',
            cancelButtonClass: 'btn btn-sm btn-danger',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                rechazarAplicado(target.attr('data-cid'));
            }
        });
    });
});
