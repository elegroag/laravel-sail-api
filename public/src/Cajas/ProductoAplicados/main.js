var dataTableConfig = {
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
        copyKeys: 'Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.',
        copySuccess: {
            1: 'Copiada 1 fila al portapapeles',
            _: 'Copiadas %d fila al portapapeles',
        },
    },
};
var _datatable = void 0;

const loadData = function(aplicados) {
    if (_datatable == void 0) {
        _datatable = $('#datatable').DataTable({
            "paging": true,
            "pageLength": 10,
            "pagingType": "full_numbers",
            "info": false,
            "columns": [{
                    "title": "Cedtra",
                    "data": "cedtra",
                    "width": "10%"
                },
                {
                    "title": "Docben",
                    "data": "docben",
                    "width": "10%"
                },
                {
                    "title": "Estado",
                    "data": "estado",
                    "width": "5%"
                },
                {
                    "title": "Fecha",
                    "data": "fecha",
                    "width": "10%"
                },
                {
                    "title": "Pin",
                    "data": "pin"
                },
                {
                    "title": "Opciones",
                    "data": "options",
                    "width": "20%"
                }
            ],
            "language": dataTableConfig
        }).draw(false);
    } else {
        _datatable.rows().clear().draw();
    }
    if (_.size(aplicados) == 0) {} else {
        let _data = new Array();
        for (const ai in aplicados) {
            let aplicado = aplicados[ai];
            let _btrechazo = '';
            if (aplicado.estado == 'A') {
                _btrechazo = `<button type="button" toggle='rechazar' data-cid='${aplicado.id}' class="btn btn-sm btn-danger">Rechazar</button>`;
            }
            let _estado = _.map(aplicado.estado, function(estado) {
                let estados = {
                    'A': 'Activo',
                    'I': 'Inactivo',
                    'R': 'Rechazado'
                };
                return (aplicado.estado) ? estados[aplicado.estado] : 'No definida';
            });
            _data[ai] = {
                "id": aplicado.id,
                "codser": aplicado.codser,
                "docben": aplicado.docben,
                "estado": _estado,
                "fecha": aplicado.fecha,
                "cedtra": aplicado.cedtra,
                "pin": aplicado.pin,
                "options": `<button type="button" toggle='detalle' data-cid='${aplicado.id}' class="btn btn-sm btn-primary">Detalles</button> ${_btrechazo}`
            };
        }
        _datatable.rows.add(_data).draw();
    }
    $('table').attr('class', 'table table-sm table-bordered');
    $('[type="search"]').addClass('row form-control');
    $('[type="search"]').css('display', 'inline-block');
    $('[type="search"]').css('width', '120px');
};

const loadDetalle = function(data) {
    let template = _.template($('#tmp_detalle_aplicado').html());
    $("#showDetalleAplicado").html(template(data));
};

const buscarLista = function() {
    let _codser = $('#codser').val();
    $.get(Utils.getKumbiaURL($Kumbia.controller + "/buscarAfiliadosAplicados/" + _codser)).done(function(response) {
        if (response.success) {
            loadData(response.data);
        }
    }).fail(function(err) {
        console.log(err.responseText);
        return false;
    });
};

const buscarAplicado = function(_id) {
    $.post(Utils.getKumbiaURL($Kumbia.controller + "/detalleAplicado/" + _id), {
        "data": void 0
    }).done(function(response) {
        if (response.success) {
            loadDetalle(response.data);
        }
    }).fail(function(err) {
        console.log(err.responseText);
        return false;
    });
};

const rechazarAplicado = function(_id) {
    let _codser = $('#codser').val();
    $.ajax({
        method: "POST",
        url: Utils.getKumbiaURL($Kumbia.controller + "/rechazar/" + _id),
        dataType: "JSON",
        cache: false,
        data: {
            codser: _codser
        }
    }).done(function(response) {
        if (response.success) {
            buscarLista();

            swal.fire({
                "title": "Notificación Alerta",
                "text": response.msj,
                "icon": "warning",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 10000
            });
        } else {
            swal.fire({
                "title": "Notificación Alerta",
                "text": response.msj,
                "icon": "warning",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 10000
            });
        }
    }).fail(function(err) {
        console.log(err.responseText);
        return false;
    });
};

$(document).ready(function() {
    $.fn.DTbl_columnCount = function() {
        return $('th', $(this).find('thead')).length;
    };

    buscarLista();

    $(document).on("click", "#reporteAplicados", function(e) {
        e.preventDefault();
        var target = $(e.currentTarget);
        swal.fire({
            title: "¡Confirmar!",
            html: "<p style='font-size:0.97rem'>¿Está seguro que desea salir, para ver los beneficiarios aplicados al servicio.?</p>",
            showCancelButton: true,
            confirmButtonClass: "btn btn-sm btn-success",
            cancelButtonClass: "btn btn-sm btn-danger",
            confirmButtonText: "SI",
            cancelButtonText: "NO"
        }).then(function(result) {
            if (result.value) {

            }
        });
    });

    $(document).on("click", "button[toggle='detalle']", function(e) {
        e.preventDefault();
        var target = $(e.currentTarget);
        let _id = target.attr('data-cid');
        buscarAplicado(_id);
    });

    $(document).on("click", "button[toggle='rechazar']", function(e) {
        e.preventDefault();
        var target = $(e.currentTarget);
        swal.fire({
            title: "¡Confirmar!",
            html: "<p style='font-size:0.97rem'>¿Está seguro que desea rechazar la solicitud del afiliado.?</p>",
            showCancelButton: true,
            confirmButtonClass: "btn btn-sm btn-success",
            cancelButtonClass: "btn btn-sm btn-danger",
            confirmButtonText: "SI",
            cancelButtonText: "NO"
        }).then(function(result) {
            if (result.value) {
                let _id = target.attr('data-cid');
                rechazarAplicado(_id);
            }
        });

    });

});
