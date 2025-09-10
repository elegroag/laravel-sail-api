<?
echo View::getContent();
echo Tag::Assets('datatables.net.bs5/css/dataTables.bootstrap5.min', 'css');
echo Tag::Assets('datatables.net/js/dataTables.min', 'js');
echo Tag::Assets('datatables.net.bs5/js/dataTables.bootstrap5.min', 'js');
?>
<input style="display:none" id="codser" value="<?= $codser ?>" />

<script type="text/template" id="tmp_detalle_aplicado">
    <div class="card-header mb-1 pt-3">
        <div id="botones" class='row'>
            <div class='col-md-10'><h3>Detalles del afiliado</h3></div>
        </div>
    </div>
    <div class="card-body pt-1">
        <div class='row pl-lg-12 pb-3'>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Cedula trabajador</label>
                <p class='pl-2 description'><%=cedtra%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Estado</label>
                <p class='pl-2 description'><%=estado_detalle%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Trabajador</label>
                <p class='pl-2 description'><%=trabajador.prinom + ' '+ trabajador.segnom + ' ' + trabajador.priape + ' ' + trabajador.segape%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Beneficiario</label>
                <p class='pl-2 description'><%=(beneficiario)? beneficiario.prinom+ ' '+beneficiario.segnom + ' '+beneficiario.priape+' '+beneficiario.segape : 'NO EXISTE'%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Cedula trabajador</label>
                <p class='pl-2 description'><%=cedtra%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Documento beneficiario</label>
                <p class='pl-2 description'><%=docben%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Empresa</label>
                <p class='pl-2 description'><%=trabajador.nit%></p>
            </div>
            <div class='col-md-6 border-top border-right border-left border-bottom'>
                <label class='form-control-label'>Zona</label>
                <p class='pl-2 description'><%=trabajador.zona_detalle%></p>
            </div>
        </div>
    </div>
</script>


<div class='card-header pt-2 pb-2' id='afiliacion_header'>
    <div id="botones" class='d-flex justify-content-end'>
        <a href="<?= $instancePath ?>admproductos/cargue_pagos/<?= $codser ?>" class='btn btn-md btn-warning'><i class="fas fa-plus"></i> Pagos</a>&nbsp;
        <a href="<?= $instancePath ?>admproductos/lista" class='btn btn-md btn-primary'><i class="fas fa-home"></i> Salir</a>&nbsp;
    </div>
    <h3 class="p-1"><?= Tag::capitalize($servicio->getServicio()) ?></h3>
    <p>Lista de afiliados que han aplicado al servicio o producto</p>
</div>

<div class="card-body p-2">
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered table-sm align-items-center mb-0 mt-0" id='datatable' style="width:100%"></table>
        </div>
        <div class="col-md-4">
            <div class="col-auto" id="showDetalleAplicado">
                <div class="card-body">
                    <p class="text-center"><?php echo Tag::image("Mercurio/consulta_aportes.jpg", "style: width:180px", "class: img-responsive"); ?></p>
                </div>
            </div>
        </div>
    </div>
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
</script>


<style>
    .table td {
        font-size: 0.91rem;
    }

    .btn-link-file {
        border-radius: 1px;
        margin-top: 5px;
        padding: 8px 5px !important;
        border: 0px;
        cursor: pointer;
        background-color: #589d62 !important;
        color: #fff !important;
        border-radius: 5px;
        font-size: 13px;
    }

    .btn-link-file:hover,
    .btn-link-file:focus,
    .btn-link-file:active {
        border: 0px;
        cursor: pointer;
    }

    .text-muted .list-group-item {
        padding: 0.2rem 1rem;
    }

    .form-control-label {
        font-size: .81rem;
        margin-bottom: 3px;
    }

    select.form-control,
    input.form-control {
        margin: 3px 0px 0px 3px;
        padding: 4px 6px;
        border-radius: 0px;
        height: initial;
        min-height: 20px;
        background-color: #fffeee;
    }

    label.error {
        color: red;
        font-size: 0.81rem;
    }

    .select2-container .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding: 0.2rem 0.3rem;
        font-size: 13px;
    }

    .list-group-item.active {
        color: #222;
        border-color: #c5e1c9;
        background-color: #c5e1c9;
    }

    h4 {
        color: #589d62;
        font-size: 1.2rem;
    }

    .input-group-append .btn-sm {
        height: 31px;
        top: 3.4px;
    }

    p {
        font-size: 0.85em;
    }
</style>