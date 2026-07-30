@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />
    <style>
        .reportesol-page-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
        }

        .reportesol-page-card .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #ffffff;
            padding: 1.1rem 1.35rem;
        }

        .reportesol-page-card .card-header h2,
        .reportesol-page-card .card-header p,
        .reportesol-page-card .card-header strong {
            color: #ffffff !important;
        }

        .reportesol-page-card .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }

        .reportesol-page-card .card-header p {
            margin: 0.35rem 0 0;
            opacity: 0.95;
            font-size: 0.875rem;
        }

        .reportesol-info {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            background: #f0f7ff;
            border: 1px solid rgba(13, 110, 253, 0.18);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            color: #334155;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .reportesol-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }

        .reportesol-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .reportesol-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.1rem 0.35rem;
            margin-bottom: 1rem;
        }

        .reportesol-filters .form-control-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .reportesol-filters .form-text {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .reportesol-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
            padding-top: 0.25rem;
        }

        .reportesol-actions .btn {
            min-width: 200px;
            min-height: 44px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
        }

        .reportesol-actions .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        .reportesol-feedback {
            margin-top: 1rem;
            display: none;
        }

        .reportesol-feedback.is-visible {
            display: block;
        }

        .reportesol-feedback .alert {
            margin-bottom: 0;
            border-radius: 10px;
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title ?? 'Reportes de Solicitudes', 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card reportesol-page-card">
                <div class="card-header text-white">
                    <h2 class="text-white mb-0">{{ $title ?? 'Reportes de Solicitudes' }}</h2>
                    <p class="text-white mb-0">Exporte solicitudes por tipo y estado, con filtros opcionales de fechas.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="reportesol-info" role="note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            El tipo de solicitud es <strong>obligatorio</strong>.
                            Estado y fechas son opcionales: si no los indica, se incluyen todos los estados del tipo seleccionado.
                            El archivo Excel se abrirá en una nueva pestaña.
                        </div>
                    </div>

                    <form
                        id="form_reportesol"
                        class="validation_form"
                        action="{{ route('cajas.reportesol.procesar') }}"
                        method="POST"
                        target="_blank"
                        autocomplete="off"
                        novalidate>
                        @csrf

                        <div class="reportesol-filters">
                            <div class="reportesol-section-title">Filtros del reporte</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_solicitud" class="form-control-label">
                                            Tipo de solicitud <span class="text-danger">*</span>
                                        </label>
                                        <select name="tipo" id="tipo_solicitud" class="form-control" required aria-required="true">
                                            <option value="">Seleccione</option>
                                            @foreach ($tipo_solicitudes as $value => $text)
                                                <option value="{{ $value }}">{{ $text }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text">Seleccione el tipo a consultar.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado_solicitud" class="form-control-label">Estado de solicitud</label>
                                        <select name="estado" id="estado_solicitud" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="A">Activos</option>
                                            <option value="D">Devueltos</option>
                                            <option value="R">Rechazados</option>
                                            <option value="C">Cancelados</option>
                                            <option value="I">Inactivos</option>
                                        </select>
                                        <small class="form-text">Opcional. Por defecto incluye todos.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_solicitud" class="form-control-label">Fecha de envío</label>
                                        <input
                                            type="text"
                                            id="fecha_solicitud"
                                            name="fecha_solicitud"
                                            class="form-control datepicker"
                                            placeholder="YYYY-MM-DD"
                                            autocomplete="off">
                                        <small class="form-text">Opcional. Filtra por fecha de radicación/envío.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_aprueba" class="form-control-label">Fecha de aprobación</label>
                                        <input
                                            type="text"
                                            id="fecha_aprueba"
                                            name="fecha_aprueba"
                                            class="form-control datepicker"
                                            placeholder="YYYY-MM-DD"
                                            autocomplete="off">
                                        <small class="form-text">Opcional. Filtra por fecha de aprobación.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reportesol-actions">
                            <button type="submit" id="btn_generar_reporte" class="btn btn-success" disabled aria-disabled="true">
                                <i class="fas fa-file-excel" aria-hidden="true"></i>
                                <span data-role="btn-label">Generar Excel</span>
                            </button>
                        </div>

                        <div id="reportesol-feedback" class="reportesol-feedback" role="status" aria-live="polite"></div>
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
    <script src="{{ versioned_asset('cajas/build/ReporteSolicitudes.js') }}"></script>
@endpush
