@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />
    <style>
        .rcs-page-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
        }

        .rcs-page-card .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #ffffff;
            padding: 1.1rem 1.35rem;
        }

        .rcs-page-card .card-header h2,
        .rcs-page-card .card-header p {
            color: #ffffff !important;
            margin: 0;
        }

        .rcs-page-card .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .rcs-page-card .card-header p {
            margin-top: 0.35rem;
            opacity: 0.95;
            font-size: 0.875rem;
        }

        .rcs-info {
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

        .rcs-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }

        .rcs-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.1rem 0.35rem;
            margin-bottom: 1rem;
        }

        .rcs-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .rcs-filters .form-control-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .rcs-filters .form-text {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .rcs-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.85rem;
            margin-top: 0.65rem;
            padding-top: 0.35rem;
        }

        .rcs-actions .btn {
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

        .rcs-actions .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        .rcs-actions .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            box-shadow: none;
            transform: none;
        }

        .rcs-actions .btn-primary {
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 100%);
            border-color: #0a58ca;
        }

        .rcs-results {
            margin-top: 1.25rem;
            display: none;
        }

        .rcs-results.is-visible {
            display: block;
        }

        .rcs-results-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .rcs-results-meta strong {
            color: #0f172a;
        }

        .rcs-resumen {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.85rem;
        }

        .rcs-chip {
            background: #e8eef7;
            border-radius: 999px;
            color: #1e3a5f;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.28rem 0.7rem;
        }

        .rcs-chip--pa { background: #d1e7dd; color: #0f5132; }
        .rcs-chip--pe { background: #fff3cd; color: #664d03; }
        .rcs-chip--de { background: #e2e3e5; color: #41464b; }
        .rcs-chip--re { background: #f8d7da; color: #842029; }

        .rcs-table-wrap {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
        }

        .rcs-table {
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .rcs-table thead th {
            background: #e8eef7;
            border-bottom: 1px solid #c5d4e8;
            white-space: nowrap;
            font-weight: 700;
            color: #1e3a5f;
        }

        .rcs-estado {
            border-radius: 999px;
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.2;
            padding: 0.2rem 0.55rem;
            white-space: nowrap;
        }

        .rcs-estado--pe { background: #fff3cd; color: #664d03; }
        .rcs-estado--pa { background: #d1e7dd; color: #0f5132; }
        .rcs-estado--de { background: #e2e3e5; color: #41464b; }
        .rcs-estado--re { background: #f8d7da; color: #842029; }

        .rcs-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 0.9rem;
            padding: 0.65rem 0.25rem 0;
        }

        .rcs-pagination-info {
            font-size: 0.82rem;
            color: #64748b;
        }

        .rcs-pagination-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem;
        }

        .rcs-pagination-controls .btn {
            min-width: 36px;
            min-height: 34px;
            padding: 0.25rem 0.55rem;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .rcs-pagination-controls .btn.is-current {
            pointer-events: none;
        }

        @media print {
            .tmp_header_adapter,
            .sidenav,
            .navbar,
            .rcs-info,
            .rcs-filters,
            .rcs-actions,
            .rcs-pagination,
            .card-header,
            footer {
                display: none !important;
            }

            .rcs-results {
                display: block !important;
            }

            .rcs-page-card {
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
            <div class="card rcs-page-card">
                <div class="card-header text-white">
                    <h2 class="text-white mb-0">{{ $title }}</h2>
                    <p class="text-white mb-0">Consulte las preventas y ventas en línea de servicios del portal ecommerce por rango de fechas y estado.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="rcs-info" role="note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            Los datos corresponden a las <strong>preventas</strong> y
                            <strong>ventas en línea</strong> del catálogo de servicios.
                            Estados: <strong>PE</strong> pendiente, <strong>PA</strong> pagado,
                            <strong>DE</strong> desestimado, <strong>RE</strong> rechazado.
                            El rango se aplica sobre la <strong>fecha de preventa</strong>.
                        </div>
                    </div>

                    <form id="form-rcs" autocomplete="off" novalidate>
                        @csrf
                        <div class="rcs-filters">
                            <div class="rcs-section-title">Filtros de consulta</div>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estado" class="form-control-label">Estado</label>
                                        <select id="estado" name="estado" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach ($estadosLabels as $codigo => $detalle)
                                                <option value="{{ $codigo }}">{{ $codigo }} — {{ $detalle }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text">Opcional.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rcs-actions">
                            <button type="submit" id="btn-consultar" class="btn btn-primary" aria-busy="false">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <span data-role="btn-label">Consultar</span>
                            </button>
                            <button type="button" id="btn-imprimir" class="btn btn-outline-secondary" disabled aria-disabled="true">
                                <i class="fas fa-print" aria-hidden="true"></i>
                                <span>Imprimir</span>
                            </button>
                        </div>
                    </form>

                    <div id="rcs-results" class="rcs-results">
                        <div class="rcs-results-meta">
                            <div>
                                Total: <strong data-role="total">0</strong>
                                <span class="text-muted small ml-1" data-role="rango"></span>
                            </div>
                            <div>
                                Valor total: <strong data-role="valor-total">$0</strong>
                                · Pagado: <strong data-role="valor-pagado">$0</strong>
                            </div>
                        </div>
                        <div id="rcs-resumen" class="rcs-resumen"></div>
                        <div class="rcs-table-wrap">
                            <table class="table table-sm table-striped rcs-table" id="tabla-rcs">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Documento</th>
                                        <th>Beneficiario</th>
                                        <th>Servicio</th>
                                        <th>Apertura</th>
                                        <th>Valor</th>
                                        <th>Estado</th>
                                        <th>Ref. ePayco</th>
                                        <th>Transaction ID</th>
                                        <th>Approval code</th>
                                        <th>Fecha preventa</th>
                                        <th>Fecha pago</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="rcs-pagination" class="rcs-pagination" hidden>
                            <div class="rcs-pagination-info" data-role="page-info"></div>
                            <div class="rcs-pagination-controls" data-role="page-controls"></div>
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
        window.ServerController = 'reporte-compras-servicios';
        window.ReporteComprasServiciosRoutes = {
            consultar: @json(route('cajas.reporte-compras-servicios.consultar')),
        };
        window.ReporteComprasServiciosEstados = @json($estadosLabels);
    </script>
    <script src="{{ versioned_asset('cajas/build/ReporteComprasServicios.js') }}"></script>
@endpush
