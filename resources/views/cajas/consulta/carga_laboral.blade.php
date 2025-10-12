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
                    <div class='card-columns'>
                        @foreach ($gener02 as $mgener02)
                        <div class='card'>
                            <div class='card-header bg-transparent text-center'>
                                <h5 class='h4 ls-1 py-0 mb-0'>{{ $mgener02->getNombre() }}</h5>
                            </div>
                            <ul class='list-group list-group-flush'>
                           {{--  @foreach ($mercurio09 as $mmercurio09)
                                @php
                                    if ($mmercurio09->getTipopc() == '2'){
                                        $condi = " estado IN('P','D') ";
                                    } else {
                                        $condi = " estado='P' ";
                                    }

                                    $result = $consultasOldServices->consultaTipopc($mmercurio09->getTipopc(), 'count', '', $mgener02->getUsuario(), $condi);
                                    $count = $result['count'];
                                @endphp

                                <li class='list-group-item d-flex justify-content-between align-items-center py-2'>
                                    <small>{{ ucwords(strtolower($mmercurio09->getDetalle())) }}</small>
                                    <span class='badge badge-md badge-primary badge-pill'>{{ $count }}</span>
                                </li>
                            @endforeach --}}
                            </ul>
                        </div>
                        @endforeach
                    </div>
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
@endpush
