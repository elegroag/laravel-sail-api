@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />
    <style>
        .oportunidad-page-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
        }

        .oportunidad-page-card .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #ffffff;
            padding: 1.1rem 1.35rem;
        }

        .oportunidad-page-card .card-header h2,
        .oportunidad-page-card .card-header p,
        .oportunidad-page-card .card-header strong {
            color: #ffffff !important;
        }

        .oportunidad-page-card .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }

        .oportunidad-page-card .card-header p {
            margin: 0.35rem 0 0;
            opacity: 0.95;
            font-size: 0.875rem;
        }

        .oportunidad-info {
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

        .oportunidad-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }

        .oportunidad-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .oportunidad-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.1rem 0.35rem;
            margin-bottom: 1rem;
        }

        .oportunidad-filters .form-control-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .oportunidad-filters .form-text {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .oportunidad-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
            padding-top: 0.25rem;
        }

        .oportunidad-actions .btn {
            min-width: 200px;
            min-height: 44px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
        }

        .oportunidad-actions .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        .oportunidad-feedback {
            margin-top: 1rem;
            display: none;
        }

        .oportunidad-feedback.is-visible {
            display: block;
        }

        .oportunidad-feedback .alert {
            margin-bottom: 0;
            border-radius: 10px;
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card oportunidad-page-card">
                <div class="card-header text-white">
                    <h2 class="text-white mb-0">{{ $title ?? 'Reporte Oportunidad Afiliaciones' }}</h2>
                    <p class="text-white mb-0">Exporte el control de oportunidad de afiliaciones según el rango de fechas y filtros opcionales.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="oportunidad-info" role="note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            Se calculan los <strong>días hábiles</strong> entre el <strong>envío a caja</strong> (evento P)
                            y el <strong>cierre</strong> del mismo radicado (feccie del evento, o hoy si sigue abierto).
                            Umbral configurado: <strong>{{ $umbralDias }} días hábiles</strong>.
                            Cada fila corresponde a un envío en Mercurio10 con solicitud viva o archivada en auditoría.
                            Se omiten solicitudes inactivas y eventos sin solicitud ni snapshot en auditoría.
                        </div>
                    </div>

                    <form id="form" autocomplete="off" novalidate>
                        @csrf

                        <div class="oportunidad-filters">
                            <div class="oportunidad-section-title">Filtros requeridos</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecini" class="form-control-label">
                                            Fecha envío a caja inicial <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="fecini"
                                            name="fecini"
                                            class="form-control datepicker"
                                            placeholder="YYYY-MM-DD"
                                            required
                                            aria-required="true"
                                            autocomplete="off">
                                        <small class="form-text">Inicio del periodo según fecha del evento P.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecfin" class="form-control-label">
                                            Fecha envío a caja final <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="fecfin"
                                            name="fecfin"
                                            class="form-control datepicker"
                                            placeholder="YYYY-MM-DD"
                                            required
                                            aria-required="true"
                                            autocomplete="off">
                                        <small class="form-text">Fin del periodo según fecha del evento P.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipafis" class="form-control-label">Tipo de afiliación</label>
                                        <select id="tipafis" name="tipafis" class="form-control" aria-describedby="tipafis-help">
                                            <option value="">Todos</option>
                                            @foreach ($mercurio09 as $tipo)
                                                <option value="{{ $tipo->tipopc }}">{{ $tipo->detalle }}</option>
                                            @endforeach
                                        </select>
                                        <small id="tipafis-help" class="form-text">Opcional. Deje “Todos” para incluir todos los tipos.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="oportunidad-actions">
                            <button
                                type="button"
                                class="btn btn-success"
                                data-toggle="exportar_reporte"
                                disabled
                                aria-disabled="true">
                                <i class="fas fa-file-excel" aria-hidden="true"></i>
                                <span data-role="btn-label">Descargar Excel</span>
                            </button>
                        </div>

                        <div id="oportunidad-feedback" class="oportunidad-feedback" role="status" aria-live="polite"></div>
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
            exportar: @json(route('cajas.reporte-oportunidad.exportar')),
        };
    </script>
    <script src="{{ versioned_asset('cajas/build/OportunidadAfiliacion.js') }}"></script>
@endpush
