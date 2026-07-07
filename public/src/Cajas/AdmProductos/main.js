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

const estadosServicio = {
    A: 'Activo',
    P: 'Pendiente',
    F: 'Finalizado',
};

let _datatable;

const loadData = (servicios) => {
    if (_datatable === undefined) {
        _datatable = $('#datatable')
            .DataTable({
                paging: true,
                pageLength: 10,
                pagingType: 'full_numbers',
                info: true,
                columns: [
                    { title: 'Código', data: 'codser' },
                    { title: 'Servicio', data: 'servicio' },
                    { title: 'Estado', data: 'estado' },
                    { title: 'Cupos', data: 'cupos' },
                    { title: '#Trabajadores', data: 'cantidad_trabajadores' },
                    { title: '#Beneficiarios', data: 'cantidad_beneficiarios' },
                    { title: 'Opciones', data: 'options' },
                ],
                language: dataTableConfig,
            })
            .draw(false);
    } else {
        _datatable.rows().clear().draw();
    }

    if (_.size(servicios) > 0) {
        const _data = [];
        for (const ai in servicios) {
            const servicio = servicios[ai];
            let _btrechazo = '';
            if (servicio.estado === 'A') {
                _btrechazo = `<button type="button" toggle='finalizar' data-cid='${servicio.id}' class="btn btn-sm btn-danger">Finalizar</button>`;
            }
            const estadoLabel = estadosServicio[servicio.estado] ?? 'No definida';

            _data[ai] = {
                id: servicio.id,
                codser: servicio.codser,
                servicio: servicio.servicio,
                estado: estadoLabel,
                cupos: servicio.cupos,
                cantidad_trabajadores: servicio.cantidad_trabajadores,
                cantidad_beneficiarios: servicio.cantidad_beneficiarios,
                options: `<button type="button" toggle='aplicados' data-cid='${servicio.codser}' class="btn btn-sm btn-success">Aplicados</button>
                    <button type="button" toggle='editar' data-cid='${servicio.id}' class="btn btn-sm btn-info">Editar</button> ${_btrechazo}`,
            };
        }
        _datatable.rows.add(_data).draw();
    }

    $('table').attr('class', 'table table-sm table-bordered');
    $('[type="search"]').addClass('row form-control');
    $('[type="search"]').css('display', 'inline-block');
    $('[type="search"]').css('width', '200px');
};

const buscarLista = () => {
    logger.info('AdmProductos:buscarLista - solicitando lista');

    window.App.trigger('ajax', {
        url: `${controller()}/buscar_lista`,
        data: {},
        callback: (response) => {
            if (response?.success) {
                logger.info('AdmProductos:buscarLista - datos recibidos', {
                    count: response.data?.length ?? 0,
                });
                loadData(response.data ?? []);
                return;
            }

            logger.warn('AdmProductos:buscarLista - respuesta sin éxito', response);
            Messages.display(response?.msj ?? 'No se pudo cargar la lista de productos.', 'error');
        },
    });
};

const finalizaServicio = (target) => {
    const id = target.attr('data-cid');
    logger.info('AdmProductos:finalizaServicio - solicitando cambio de estado', { id });

    window.App.trigger('ajax', {
        url: `${controller()}/cambio_estado`,
        data: {
            id,
            estado: 'F',
        },
        callback: (response) => {
            if (response?.success) {
                logger.info('AdmProductos:finalizaServicio - servicio finalizado', { id });
                buscarLista();
                swal.fire({
                    title: 'Notificación',
                    text: response.msj,
                    icon: 'warning',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 10000,
                });
                return;
            }

            logger.warn('AdmProductos:finalizaServicio - respuesta sin éxito', response);
            swal.fire({
                title: 'Notificación Alerta',
                text: response?.msj ?? 'No fue posible finalizar el servicio.',
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

    $(document).on('click', "button[toggle='aplicados']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        swal.fire({
            title: '¡Confirmar!',
            html: "<p style='font-size:0.97rem'>¿Está seguro que desea salir, para ver los beneficiarios aplicados al servicio.?</p>",
            showCancelButton: true,
            confirmButtonClass: 'btn btn-sm btn-success',
            cancelButtonClass: 'btn btn-sm btn-danger',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                window.location.href = window.App.url(
                    `aplicados/${target.attr('data-cid')}`,
                    controller(),
                );
            }
        });
    });

    $(document).on('click', "button[toggle='editar']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        swal.fire({
            title: '¡Confirmar!',
            html: "<p style='font-size:0.97rem'>¿Está seguro que desea salir, para editar el registro del servicio producto.?</p>",
            showCancelButton: true,
            confirmButtonClass: 'btn btn-sm btn-success',
            cancelButtonClass: 'btn btn-sm btn-danger',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                window.location.href = window.App.url(`editar/${target.attr('data-cid')}`, controller());
            }
        });
    });

    $(document).on('click', "button[toggle='finalizar']", (e) => {
        e.preventDefault();
        const target = $(e.currentTarget);
        swal.fire({
            title: '¡Confirmar!',
            html: "<p style='font-size:0.97rem'>¿Está seguro que desea finalizar el servicio o producto.?</p>",
            showCancelButton: true,
            confirmButtonClass: 'btn btn-sm btn-success',
            cancelButtonClass: 'btn btn-sm btn-danger',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.value) {
                finalizaServicio(target);
            }
        });
    });
});
