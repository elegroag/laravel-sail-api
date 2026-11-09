@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />
    <style>
        .badge-conciliada { background-color: #198754; color: #fff; }
        .badge-pagada-sin-venta { background-color: #dc3545; color: #fff; }
        .badge-valor-diferente { background-color: #fd7e14; color: #fff; }
        .badge-venta-sin-pa { background-color: #6c757d; color: #fff; }
        .badge-aceptada-epayco-no-pa { background-color: #6f42c1; color: #fff; }
        .badge-sin-tx-epayco { background-color: #0dcaf0; color: #000; }
        .badge-venta-no-aprobada { background-color: #212529; color: #fff; }
        .conciliacion-resumen .card { min-width: 140px; }
    </style>
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <form id="form_conciliacion" class="row g-2 align-items-end mb-3">
                        <div class="col-auto">
                            <label for="fecha_inicio" class="form-label mb-0 small">Desde</label>
                            <input type="text" id="fecha_inicio" name="fecha_inicio"
                                   class="form-control form-control-sm datepicker"
                                   placeholder="AAAA-MM-DD"
                                   value="{{ $fecha_inicio }}"
                                   autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <label for="fecha_fin" class="form-label mb-0 small">Hasta</label>
                            <input type="text" id="fecha_fin" name="fecha_fin"
                                   class="form-control form-control-sm datepicker"
                                   placeholder="AAAA-MM-DD"
                                   value="{{ $fecha_fin }}"
                                   autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <label for="filtro_estado" class="form-label mb-0 small">Estado</label>
                            <select id="filtro_estado" name="estado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach ($filtro_estados as $codigo => $label)
                                    <option value="{{ $codigo }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary" id="btn_conciliar">
                                <i class="fas fa-balance-scale me-1"></i> Conciliar
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-sm btn-outline-success" id="btn_exportar_csv">
                                <i class="fas fa-file-csv me-1"></i> Exportar CSV
                            </a>
                        </div>
                    </form>

                    <div id="resumen" class="conciliacion-resumen mb-3"></div>
                    <div id="mensaje" class="mb-2"></div>
                    <div id="consulta" class="table-responsive">
                        <div class="text-muted small">Seleccione un rango de fechas y pulse Conciliar.</div>
                    </div>
                    <div id="paginate" class="card-footer py-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        window.ServerController = 'conciliacion-servicios';
        window.routesConciliacion = {
            buscar: "{{ route('conciliacion-servicios.buscar') }}",
            reporte: "{{ route('conciliacion-servicios.reporte', ['format' => 'csv']) }}",
            registrarSubsidioBase: "{{ url('/cajas/admservicios/registrar-subsidio') }}",
        };
    </script>
    <script src="{{ versioned_asset('cajas/build/ConciliacionServicios.js') }}"></script>
@endpush
