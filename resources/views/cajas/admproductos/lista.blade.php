<?
echo View::getContent();
echo Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css');
echo Tag::Assets('datatables.net/js/dataTables.min', 'js');
echo Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js');
?>

<div class='card-header pt-2 pb-2' id='afiliacion_header'>
    <div class='row'>
        <div class='col'>
            <div id="botones" class='d-flex justify-content-end'>
                <a href="<?= $instancePath ?>admproductos/nuevo" class='btn btn-info'><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuevo</a>&nbsp;
            </div>
        </div>
    </div>
</div>
<div id='consulta' class='table-responsive'>
    <table class="table-sm align-items-center mt-2" id='datatable' style="width:100%"></table>
</div>


<script type="text/javascript">
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

    const loadData = function(servicios) {
        if (_datatable == void 0) {
            _datatable = $('#datatable').DataTable({
                "paging": true,
                "pageLength": 10,
                "pagingType": "full_numbers",
                "info": true,
                "columns": [{
                        "title": "Código",
                        "data": "codser"
                    },
                    {
                        "title": "Servicio",
                        "data": "servicio"
                    },
                    {
                        "title": "Estado",
                        "data": "estado"
                    },
                    {
                        "title": "Cupos",
                        "data": "cupos"
                    },
                    {
                        "title": "#Trabajadores",
                        "data": "cantidad_trabajadores"
                    },
                    {
                        "title": "#Beneficiarios",
                        "data": "cantidad_beneficiarios"
                    },
                    {
                        "title": "Opciones",
                        "data": "options"
                    }
                ],
                "language": dataTableConfig
            }).draw(false);
        } else {
            _datatable.rows().clear().draw();
        }
        if (_.size(servicios) == 0) {} else {
            let _data = new Array();
            for (const ai in servicios) {
                let servicio = servicios[ai];
                let _btrechazo = '';
                if (servicio.estado == 'A') {
                    _btrechazo = `<button type="button" toggle='finalizar' data-cid='${servicio.id}' class="btn btn-sm btn-danger">Finalizar</button>`;
                }
                let _estado = _.map(servicio.estado, function(estado) {
                    let estados = {
                        'A': 'Activo',
                        'P': 'Pendiente',
                        'F': 'Finalizado'
                    };
                    return (servicio.estado) ? estados[servicio.estado] : 'No definida';
                });

                _data[ai] = {
                    "id": servicio.id,
                    "codser": servicio.codser,
                    "servicio": servicio.servicio,
                    "estado": _estado,
                    "cupos": servicio.cupos,
                    "cantidad_trabajadores": servicio.cantidad_trabajadores,
                    "cantidad_beneficiarios": servicio.cantidad_beneficiarios,
                    "options": `<button type="button" toggle='aplicados' data-cid='${servicio.codser}' class="btn btn-sm btn-success">Aplicados</button>
                        <button type="button" toggle='editar' data-cid='${servicio.id}' class="btn btn-sm btn-info">Editar</button> ${_btrechazo}`
                };
            }
            _datatable.rows.add(_data).draw();
        }
        $('table').attr('class', 'table table-sm table-bordered');
        $('[type="search"]').addClass('row form-control');
        $('[type="search"]').css('display', 'inline-block');
        $('[type="search"]').css('width', '200px');
    };

    const buscarLista = function() {
        $.get(Utils.getKumbiaURL($Kumbia.controller + "/buscarLista")).done(function(response) {
            if (response.success) {
                loadData(response.data);
            }
        }).fail(function(err) {
            console.log(err.responseText);
            return false;
        });
    };

    const finalizaServicio = function(target) {
        $.ajax({
            method: "POST",
            url: Utils.getKumbiaURL($Kumbia.controller + "/changeEstado"),
            dataType: "JSON",
            cache: false,
            data: {
                "id": target.attr("data-cid"),
                'estado': 'F'
            }
        }).done(function(response) {
            if (response.success) {
                buscarLista();
                swal.fire({
                    "title": "Notificación",
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

        $(document).on("click", "button[toggle='aplicados']", function(e) {
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
                    window.location.href = Utils.getKumbiaURL($Kumbia.controller + "/aplicados/" + target.attr('data-cid'));
                }
            });
        });

        $(document).on("click", "button[toggle='editar']", function(e) {
            e.preventDefault();
            var target = $(e.currentTarget);
            swal.fire({
                title: "¡Confirmar!",
                html: "<p style='font-size:0.97rem'>¿Está seguro que desea salir, para editar el registro del servicio producto.?</p>",
                showCancelButton: true,
                confirmButtonClass: "btn btn-sm btn-success",
                cancelButtonClass: "btn btn-sm btn-danger",
                confirmButtonText: "SI",
                cancelButtonText: "NO"
            }).then(function(result) {
                if (result.value) {
                    window.location.href = Utils.getKumbiaURL($Kumbia.controller + "/editar/" + target.attr('data-cid'));
                }
            });
        });

        $(document).on("click", "button[toggle='finalizar']", function(e) {
            e.preventDefault();
            var target = $(e.currentTarget);
            swal.fire({
                title: "¡Confirmar!",
                html: "<p style='font-size:0.97rem'>¿Está seguro que desea finalizar el servicio o producto.?</p>",
                showCancelButton: true,
                confirmButtonClass: "btn btn-sm btn-success",
                cancelButtonClass: "btn btn-sm btn-danger",
                confirmButtonText: "SI",
                cancelButtonText: "NO"
            }).then(function(result) {
                if (result.value) {
                    finalizaServicio(target);
                }
            });
        });
    });
</script>