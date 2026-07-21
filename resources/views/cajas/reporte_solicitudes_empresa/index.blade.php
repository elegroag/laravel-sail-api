@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />
    <style>
        .rse-page-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
        }

        .rse-page-card .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #ffffff;
            padding: 1.1rem 1.35rem;
        }

        .rse-page-card .card-header h2,
        .rse-page-card .card-header p {
            color: #ffffff !important;
            margin: 0;
        }

        .rse-page-card .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .rse-page-card .card-header p {
            margin-top: 0.35rem;
            opacity: 0.95;
            font-size: 0.875rem;
        }

        .rse-info {
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

        .rse-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }

        .rse-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.1rem 0.35rem;
            margin-bottom: 1rem;
        }

        .rse-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .rse-filters .form-control-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .rse-filters .form-text {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .rse-tipos {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.25rem;
            margin-bottom: 0.75rem;
        }

        .rse-tipos .custom-control {
            min-height: 1.5rem;
        }

        .rse-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.85rem;
            margin-top: 0.65rem;
            padding-top: 0.35rem;
        }

        .rse-actions .btn {
            min-width: 180px;
            min-height: 46px;
            padding: 0.65rem 1.35rem;
            font-weight: 600;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .rse-actions .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        .rse-actions .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            box-shadow: none;
            transform: none;
        }

        .rse-actions .btn-primary {
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 100%);
            border-color: #0a58ca;
        }

        .rse-actions .btn-print {
            color: #fff;
            background: linear-gradient(120deg, #64748b 0%, #475569 100%);
            border-color: #475569;
        }

        .rse-actions .btn-print:hover:not(:disabled) {
            color: #fff;
            background: linear-gradient(120deg, #475569 0%, #334155 100%);
            border-color: #334155;
        }

        .rse-results {
            margin-top: 1.25rem;
            display: none;
        }

        .rse-results.is-visible {
            display: block;
        }

        .rse-results-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .rse-results-meta strong {
            color: #0f172a;
        }

        .rse-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 0.9rem;
            padding: 0.65rem 0.25rem 0;
        }

        .rse-pagination-info {
            font-size: 0.82rem;
            color: #64748b;
        }

        .rse-pagination-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem;
        }

        .rse-pagination-controls .btn {
            min-width: 36px;
            min-height: 34px;
            padding: 0.25rem 0.55rem;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .rse-pagination-controls .btn.is-current {
            pointer-events: none;
        }

        .rse-table-wrap {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
        }

        .rse-table {
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .rse-table thead th {
            background: #e8eef7;
            border-bottom: 1px solid #c5d4e8;
            white-space: nowrap;
            font-weight: 700;
            color: #1e3a5f;
        }

        @media print {
            .tmp_header_adapter,
            .sidenav,
            .navbar,
            .rse-info,
            .rse-filters,
            .rse-actions,
            .rse-pagination,
            .card-header,
            footer {
                display: none !important;
            }

            .rse-results {
                display: block !important;
            }

            .rse-page-card {
                box-shadow: none;
                border: 0;
            }

            .main-content,
            .container-fluid {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="card rse-page-card">
                <div class="card-header text-white">
                    <h2 class="text-white mb-0">{{ $title }}</h2>
                    <p class="text-white mb-0">Consulte en HTML las solicitudes cargadas por una empresa aportante en un rango de fechas.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="rse-info" role="note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            Use el <strong>documento</strong> y <strong>tipo de documento</strong> de la empresa aportante.
                            Puede filtrar por trabajador, cónyuge y/o beneficiario, y por estado de la solicitud.
                        </div>
                    </div>

                    <form id="form-rse" autocomplete="off" novalidate>
                        @csrf
                        <div class="rse-filters">
                            <div class="rse-section-title">Identificación de la empresa</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="documento" class="form-control-label">
                                            Documento (NIT) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="documento" name="documento" class="form-control" maxlength="20" required aria-required="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="coddoc" class="form-control-label">
                                            Tipo documento <span class="text-danger">*</span>
                                        </label>
                                        <select id="coddoc" name="coddoc" class="form-control" required aria-required="true">
                                            <option value="">Seleccione</option>
                                            @foreach ($tiposDocumento as $tipoDoc)
                                                <option value="{{ $tipoDoc->coddoc }}">
                                                    {{ $tipoDoc->codrua ?: $tipoDoc->coddoc }} — {{ $tipoDoc->detdoc }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estado" class="form-control-label">Estado de solicitud</label>
                                        <select id="estado" name="estado" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach ($estadosLabels as $codigo => $detalle)
                                                <option value="{{ $codigo }}">{{ $detalle }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text">Opcional.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="rse-section-title mt-2">Rango de fechas (fecha de solicitud)</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecini" class="form-control-label">
                                            Fecha inicial <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="fecini" name="fecini" class="form-control datepicker" placeholder="YYYY-MM-DD" required aria-required="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecfin" class="form-control-label">
                                            Fecha final <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="fecfin" name="fecfin" class="form-control datepicker" placeholder="YYYY-MM-DD" required aria-required="true" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="rse-section-title mt-2">Tipos de solicitud <span class="text-danger">*</span></div>
                            <div class="rse-tipos">
                                @foreach ($tipopcLabels as $tipopc => $detalle)
                                    <div class="custom-control custom-checkbox">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input tipopc-check"
                                            id="tipopc_{{ $tipopc }}"
                                            name="tipopcs[]"
                                            value="{{ $tipopc }}"
                                            checked>
                                        <label class="custom-control-label" for="tipopc_{{ $tipopc }}">{{ $detalle }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="rse-actions">
                            <button type="submit" id="btn-consultar" class="btn btn-primary" aria-busy="false">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <span data-role="btn-label">Consultar</span>
                            </button>
                            <button type="button" id="btn-imprimir" class="btn btn-print" disabled aria-disabled="true">
                                <i class="fas fa-print" aria-hidden="true"></i>
                                <span>Imprimir</span>
                            </button>
                        </div>
                    </form>

                    <div id="rse-results" class="rse-results">
                        <div class="rse-results-meta">
                            <div>
                                Resultados para empresa
                                <strong data-role="empresa-doc"></strong>
                                — Total: <strong data-role="total">0</strong>
                                <span class="text-muted small ml-1" data-role="cache-hint" hidden></span>
                            </div>
                        </div>
                        <div class="rse-table-wrap">
                            <table class="table table-sm table-striped rse-table" id="tabla-rse">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Radicado</th>
                                        <th>Documento afiliado</th>
                                        <th>Nombre</th>
                                        <th>NIT laboral</th>
                                        <th>Fecha solicitud</th>
                                        <th>Estado</th>
                                        <th>Fecha estado/aprobación</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="rse-pagination" class="rse-pagination" hidden>
                            <div class="rse-pagination-info" data-role="page-info"></div>
                            <div class="rse-pagination-controls" data-role="page-controls"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.ServerController = 'reporte-solicitudes-empresa';
        window.ReporteSolicitudesEmpresaRoutes = {
            consultar: @json(route('cajas.reporte-solicitudes-empresa.consultar')),
        };
    </script>
    <script src="{{ asset('cajas/build/ReporteSolicitudesEmpresa.js') }}"></script>
@endpush
