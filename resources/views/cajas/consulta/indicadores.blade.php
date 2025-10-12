@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0 m-3">
                    <form id="form" class="validation_form" autocomplete="off" novalidate>
                        <div class="row">
                            <div class="col-md-4 ml-auto">
                                <div class="form-group">
                                    <label for="fecini" class="form-control-label">Fecha Inicial</label>
                                    <input type="text" id="fecini" name="fecini" class="form-control" placeholder="Fecha Inicial" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                                    <input type="text" id="fecfin" name="fecfin" class="form-control" placeholder="Fecha Final" required>
                                </div>
                            </div>
                            <div class="col-md-auto d-flex mr-auto">
                                <button type="button" class="btn btn-primary align-self-center" onclick="consulta_indicadores();">Consultar</button>
                            </div>
                        </div>
                    </form>
                    <div id='consulta' class='table-responsive'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.ServerController = 'consulta';
    </script>

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal'])

    <script type="text/javascript">
        function reporte_excel_carga_laboral() {
            window.location.href = Utils.getKumbiaURL(
                $Kumbia.controller + '/reporte_excel_carga_laboral',
            );
        }

        function reporte_excel_indicadores() {
            var validator = $('#form').validate({
                rules: {
                    fecini: {
                        required: true
                    },
                    fecfin: {
                        required: true
                    },
                },
            });
            if (!$('#form').valid()) {
                return;
            }
            window.location.href = Utils.getKumbiaURL(
                $Kumbia.controller +
                '/reporte_excel_indicadores/' +
                $('#fecini').val() +
                '/' +
                $('#fecfin').val(),
            );
        }

        function consulta_indicadores() {
            var validator = $('#form').validate({
                rules: {
                    fecini: {
                        required: true
                    },
                    fecfin: {
                        required: true
                    },
                },
            });
            if (!$('#form').valid()) {
                return;
            }
            $.ajax({
                    type: 'POST',
                    url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_indicadores'),
                    data: {
                        fecini: $('#fecini').val(),
                        fecfin: $('#fecfin').val(),
                    },
                })
                .done(function(transport) {
                    var response = transport;
                    $('#consulta').html(response);
                })
                .fail(function(jqXHR, textStatus) {
                    alert('Request failed: ' + textStatus);
                });
        }

        function reporte_auditoria() {
            var validator = $('#form').validate({
                rules: {
                    tipopc: {
                        required: true
                    },
                    fecini: {
                        required: true
                    },
                    fecfin: {
                        required: true
                    },
                },
            });
            if (!$('#form').valid()) {
                return;
            }
            $('#form').submit();
        }

        function consulta_auditoria() {
            var validator = $('#form').validate({
                rules: {
                    tipopc: {
                        required: true
                    },
                    fecini: {
                        required: true
                    },
                    fecfin: {
                        required: true
                    },
                },
            });
            if (!$('#form').valid()) {
                return;
            }
            $.ajax({
                    type: 'POST',
                    url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_auditoria'),
                    data: {
                        tipopc: $('#tipopc').val(),
                        fecini: $('#fecini').val(),
                        fecfin: $('#fecfin').val(),
                    },
                })
                .done(function(transport) {
                    var response = transport;
                    $('#consulta').html(response);
                })
                .fail(function(jqXHR, textStatus) {
                    alert('Request failed: ' + textStatus);
                });
        }

        function info(tipopc, id) {
            $.ajax({
                    type: 'POST',
                    url: Utils.getKumbiaURL($Kumbia.controller + '/info'),
                    data: {
                        tipopc: tipopc,
                        id: id,
                    },
                })
                .done(function(transport) {
                    var response = transport;
                    $('#result_info').html(response);
                    $('#capture-modal-info').modal();
                })
                .fail(function(jqXHR, textStatus) {
                    alert('Request failed: ' + textStatus);
                });
        }

        function consulta_activacion_masiva() {
            var validator = $('#form').validate({
                rules: {
                    nit: {
                        required: true
                    },
                    fecini: {
                        required: true
                    },
                    fecfin: {
                        required: true
                    },
                },
            });
            if (!$('#form').valid()) {
                return;
            }
            $.ajax({
                    type: 'POST',
                    url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_activacion_masiva'),
                    data: {
                        nit: $('#nit').val(),
                        fecini: $('#fecini').val(),
                        fecfin: $('#fecfin').val(),
                    },
                })
                .done(function(transport) {
                    var response = transport;
                    $('#consulta').html(response);
                })
                .fail(function(jqXHR, textStatus) {
                    alert('Request failed: ' + textStatus);
                });
        }

        function descarga_activacion(element) {
            window.open(Utils.getURL('temp/' + element.innerHTML));
        }
    </script>


    <script src="{{ asset('cajas/build/Consulta.js') }}"></script>
@endpush



