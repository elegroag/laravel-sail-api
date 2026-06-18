@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0 m-3">
                    <form id="form" autocomplete="off" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modalidad" class="form-control-label">Modalidad</label>
                                    <select id="modalidad" name="modalidad" class="form-control" required>
                                        <option value="aportante">Por aportante (empresa)</option>
                                        <option value="trabajador">Por trabajador y beneficiarios</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="campo_fecha" class="form-control-label">Campo de fecha</label>
                                    <select id="campo_fecha" name="campo_fecha" class="form-control">
                                        <option value="fecsol">Fecha de solicitud</option>
                                        <option value="sat_fecapr">Fecha de registro SISU</option>
                                        <option value="fecapr">Fecha de afiliación</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estado" class="form-control-label">Estado</label>
                                    <select id="estado" name="estado" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->codest }}">{{ $estado->detalle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecini" class="form-control-label">Fecha inicial</label>
                                    <input type="text" id="fecini" name="fecini" class="form-control datepicker" placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecfin" class="form-control-label">Fecha final</label>
                                    <input type="text" id="fecfin" name="fecfin" class="form-control datepicker" placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nit" class="form-control-label">Aportante (NIT)</label>
                                    <input type="text" id="nit" name="nit" class="form-control" maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cedtra" class="form-control-label">Trabajador (documento)</label>
                                    <input type="text" id="cedtra" name="cedtra" class="form-control" maxlength="20">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tipafis" class="form-control-label">Tipo de afiliación</label>
                                    <select id="tipafis" name="tipafis[]" class="form-control" multiple>
                                        @foreach ($mercurio09 as $tipo)
                                            <option value="{{ $tipo->tipopc }}">{{ $tipo->detalle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-danger" data-toggle="generar_reporte">Generar Excel</button>
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
        window.ServerController = 'reporte-oportunidad';
        window.ReporteOportunidadRoutes = {
            aportante: @json(route('cajas.reporte-oportunidad.por-aportante')),
            trabajador: @json(route('cajas.reporte-oportunidad.por-trabajador')),
        };
    </script>
    <script src="{{ asset('cajas/build/OportunidadAfiliacion.js') }}"></script>
@endpush
