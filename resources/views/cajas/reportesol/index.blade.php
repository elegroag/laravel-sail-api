@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title ?? 'Reportes de Solicitudes', 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <form id="form_reportesol" class="validation_form" action="{{ route('cajas.reportesol.procesar') }}" method="POST" target="_blank" autocomplete="off" novalidate>
                        @csrf
                        <p class="m-2 text-center">Reportes de Solicitudes, por tipo de solicitud, en sus diferentes estados</p>
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="form-group">
                                    <label for="tipo_solicitud">Tipo de Solicitud</label>
                                    <select name="tipo" id="tipo_solicitud" class="form-control" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($tipo_solicitudes as $value => $text)
                                            <option value="{{ $value }}">{{ $text }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="estado_solicitud">Estado de Solicitud</label>
                                    <select name="estado" id="estado_solicitud" class="form-control">
                                        <option value="">TODOS</option>
                                        <option value="A">Activos</option>
                                        <option value="D">Devueltos</option>
                                        <option value="R">Rechazados</option>
                                        <option value="C">Cancelados</option>
                                        <option value="I">Inactivos</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_solicitud">Fecha de envío</label>
                                    <input type="date" id="fecha_solicitud" name="fecha_solicitud" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="fecha_aprueba">Fecha de aprobación</label>
                                    <input type="date" id="fecha_aprueba" name="fecha_aprueba" class="form-control">
                                </div>

                                <div class="form-group text-center mt-2">
                                    <button type="submit" id="btn_generar_reporte" class="btn btn-primary">Generar Reporte</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.ServerController = 'reportesol';
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            const $form = $('#form_reportesol');
            $form.validate({
                rules: {
                    tipo: { required: true },
                },
                submitHandler: function (form) {
                    form.submit();
                },
            });
        });
    </script>
@endpush
