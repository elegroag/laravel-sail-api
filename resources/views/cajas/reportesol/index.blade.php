@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title ?? 'Reportes de Solicitudes', 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div id='consulta' class='table-responsive'></div>
                    <div id='paginate' class='card-footer py-4'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro ?? []])

    <script id='tmp_form' type="text/template">
        <form id="form_reportesol" class="validation_form" autocomplete="off" novalidate>
            <p class="m-2 text-center">Reportes de Solicitudes, por tipo de solicitud, en sus diferentes estados</p>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="form-group">
                        <label for="tipo_solicitud">Tipo de Solicitud</label>
                        <select name="tipo_solicitud" id="tipo_solicitud" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach ($tipo_solicitudes as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class='form-group'>
                        <label for="estado_solicitud">Estado de Solicitud</label>
                        <select name="estado_solicitud" id="estado_solicitud" class="form-control">
                            <option value="">TODOS</option>
                            <option value="A">Activos</option>
                            <option value="D">Devueltos</option>
                            <option value="R">Rechazados</option>
                            <option value="C">Cancelados</option>
                            <option value="I">Inactivos</option>
                        </select>
                    </div>

                    <div class='form-group'>
                        <label for="fecha_solicitud">Fecha de envío</label>
                        <input type="date" id="fecha_solicitud" name="fecha_solicitud" class="form-control">
                    </div>

                    <div class='form-group'>
                        <label for="fecha_aprueba">Fecha de aprobación</label>
                        <input type="date" id="fecha_aprueba" name="fecha_aprueba" class="form-control">
                    </div>

                    <div class='form-group text-center mt-2'>
                        <button type="button" id="btn_generar_reporte" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'reportesol';
    </script>
@endpush


<script type="text/javascript">
    function downLoadFile(transfer) {
        const {
            url,
            filename
        } = transfer;
        const link = document.createElement('a');
        link.href = Utils.getKumbiaURL(url + '/' + filename);
        link.download = filename;
        console.log(link);
        link.click();
    }

    $(document).ready(function() {

        $("input[date='date']").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });

        $(document).on('click', '#btn_generar_reporte', function(e) {
            e.preventDefault();
            var tipo_solicitud = $('#tipo_solicitud').val();
            var estado_solicitud = $('#estado_solicitud').val();
            var fecha_solicitud = $('#fecha_solicitud').val();
            var fecha_aprueba = $('#fecha_aprueba').val();

            $.ajax({
                url: Utils.getKumbiaURL('reportesol/procesar'),
                type: 'POST',
                data: {
                    tipo: tipo_solicitud,
                    estado: estado_solicitud,
                    fecha_solicitud: fecha_solicitud,
                    fecha_aprueba: fecha_aprueba
                },
                success: function(response) {
                    downLoadFile(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });
    });
</script>
