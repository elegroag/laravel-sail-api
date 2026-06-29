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
                    <p class="text-muted small mb-3">
                        Control de oportunidad: dias habiles entre la fecha de solicitud y la fecha de aprobacion.
                        Umbral configurado: <strong>{{ $umbralDias }}</strong> dias habiles.
                    </p>
                    <form id="form" autocomplete="off" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecini" class="form-control-label">Fecha solicitud inicial</label>
                                    <input type="text" id="fecini" name="fecini" class="form-control datepicker" placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecfin" class="form-control-label">Fecha solicitud final</label>
                                    <input type="text" id="fecfin" name="fecfin" class="form-control datepicker" placeholder="YYYY-MM-DD" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="estado" class="form-control-label">Estado solicitud</label>
                                    <select id="estado" name="estado" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->codest }}">{{ $estado->detalle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipafis" class="form-control-label">Tipo de afiliacion</label>
                                    <select id="tipafis" name="tipafis[]" class="form-control" multiple>
                                        @foreach ($mercurio09 as $tipo)
                                            <option value="{{ $tipo->tipopc }}">{{ $tipo->detalle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nit" class="form-control-label">Aportante (NIT)</label>
                                    <input type="text" id="nit" name="nit" class="form-control" maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cedtra" class="form-control-label">Documento trabajador</label>
                                    <input type="text" id="cedtra" name="cedtra" class="form-control" maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-4 pt-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="solo_pendientes" name="solo_pendientes" value="1">
                                        <label class="custom-control-label" for="solo_pendientes">Solo pendientes (sin fecha de aprobacion)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-4 pt-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="solo_vencidos" name="solo_vencidos" value="1">
                                        <label class="custom-control-label" for="solo_vencidos">Solo vencidos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-primary mr-2" data-toggle="previsualizar_reporte">Previsualizar</button>
                            <button type="button" class="btn btn-danger" data-toggle="exportar_reporte" disabled>Descargar Excel</button>
                        </div>
                    </form>

                    <div id="resumen" class="mt-4 d-none">
                        <h5 class="mb-3">Resumen del periodo</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Total solicitudes</th>
                                        <th>En termino</th>
                                        <th>Vencidas</th>
                                        <th>En tramite</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="resumen_total">0</td>
                                        <td id="resumen_en_termino">0</td>
                                        <td id="resumen_vencido">0</td>
                                        <td id="resumen_en_tramite">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2 mb-0" id="resumen_nota"></p>
                    </div>
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
            previsualizar: @json(route('cajas.reporte-oportunidad.previsualizar')),
            exportar: @json(route('cajas.reporte-oportunidad.exportar')),
        };
    </script>
    <script src="{{ asset('cajas/build/OportunidadAfiliacion.js') }}"></script>
@endpush
