@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <style>
        .consulta-page-card {
            border-radius: 16px;
        }

        .consulta-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .consulta-summary-card {
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid rgba(13, 110, 253, 0.12);
            border-radius: 14px;
            padding: 1rem 1.15rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .consulta-summary-card-accent {
            background: linear-gradient(145deg, #fff5f5 0%, #ffffff 100%);
            border-color: rgba(220, 53, 69, 0.18);
        }

        .consulta-summary-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .consulta-summary-value {
            display: block;
            font-size: 1.2rem;
            line-height: 1.2;
            color: #212529;
        }

        .consulta-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 2.5rem 1rem;
            border: 1px dashed #ced4da;
            border-radius: 14px;
            background: #f8f9fa;
            color: #6c757d;
            text-align: center;
        }

        .consulta-empty-state i {
            font-size: 1.75rem;
            color: #adb5bd;
        }

        .consulta-data-table {
            font-size: 0.84rem;
            margin-bottom: 0;
        }

        .consulta-data-table thead th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 0;
            padding: 0.75rem 0.65rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        .consulta-data-table tbody td {
            padding: 0.7rem 0.65rem;
            vertical-align: middle;
            color: #334155;
        }

        .consulta-data-table tbody tr:hover {
            background: #f8fbff;
        }

        .consulta-company,
        .consulta-name {
            min-width: 160px;
            white-space: normal;
        }

        .consulta-motivo {
            min-width: 220px;
            white-space: normal;
        }

        .consulta-badge-warning {
            display: inline-flex;
            align-items: center;
            padding: 0.18rem 0.55rem;
            border-radius: 999px;
            background: #fff3cd;
            color: #856404;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .consulta-dt-toolbar .dataTables_length label,
        .consulta-dt-toolbar .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        .consulta-dt-toolbar .dataTables_filter input {
            min-width: 220px;
        }

        .consulta-dt-footer {
            font-size: 0.875rem;
            color: #64748b;
        }

        .consulta-dt-footer .pagination {
            margin-bottom: 0;
            justify-content: flex-end;
        }

        #consulta {
            max-width: 100%;
        }

        #consulta .dt-scroll {
            max-width: 100%;
        }

        #consulta .dataTables_wrapper {
            width: 100%;
            min-width: 0;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script type="text/template" id="templateNoGiro">
    <%
        var periodosUnicos = _.uniq(motivos.map(function (item) {
            return item.pergir;
        })).length;
        var beneficiariosUnicos = _.uniq(motivos.map(function (item) {
            return item.nombre;
        })).length;
    %>

    <div class="consulta-results">
        <div class="consulta-summary-grid">
            <div class="consulta-summary-card consulta-summary-card-accent">
                <span class="consulta-summary-label">Registros sin giro</span>
                <strong class="consulta-summary-value"><%= motivos.length %></strong>
            </div>
            <div class="consulta-summary-card">
                <span class="consulta-summary-label">Periodos afectados</span>
                <strong class="consulta-summary-value"><%= periodosUnicos %></strong>
            </div>
            <div class="consulta-summary-card">
                <span class="consulta-summary-label">Beneficiarios</span>
                <strong class="consulta-summary-value"><%= beneficiariosUnicos %></strong>
            </div>
        </div>

        <% if (motivos.length === 0) { %>
            <div class="consulta-empty-state">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <strong>No hay registros de no giro</strong>
                <span>No se encontraron periodos sin giro en el rango consultado.</span>
            </div>
        <% } else { %>
            <table id="dataTable" class="table table-hover consulta-data-table align-middle w-100">
                <thead>
                    <tr>
                        <th scope="col">Periodo girado</th>
                        <th scope="col">Periodo pagado</th>
                        <th scope="col">Razón social</th>
                        <th scope="col">Nombre beneficiario</th>
                        <th scope="col">Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <% _.each(motivos, function(item) { %>
                        <tr>
                            <td><span class="consulta-badge-warning"><%= item.pergir %></span></td>
                            <td><%= item.periodo %></td>
                            <td class="consulta-company"><%= item.razsoc %></td>
                            <td class="consulta-name"><%= item.nombre %></td>
                            <td class="consulta-motivo"><%= item.motivo %></td>
                        </tr>
                    <% }); %>
                </tbody>
            </table>
        <% } %>
    </div>
    </script>

    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'subsidio';
    </script>

    <script src="{{ versioned_asset('mercurio/build/ConsultasTrabajador.js') }}"></script>
@endpush

@section('content')
<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 consulta-page-card">
        <div class="card-header border-0 pb-2 pb-md-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h2 class="h5 mb-1">{{ $title ?? 'Consulta No Giro' }}</h2>
                    <p class="mb-0 text-sm text-muted">
                        Consulta los periodos en los que no se realizó el giro y los motivos asociados al beneficiario.
                    </p>
                </div>
                <div class="text-md-end">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2"
                        id="bt_consulta_nogiro">
                        <i class="fas fa-search me-1"></i>
                        <span>Consultar</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <form id="form" class="validation_form mb-4" autocomplete="off" novalidate>
                <div class="row g-3">
                    <div class="col-md-3 col-lg-2">
                        <div class="form-group mb-0">
                            <label for="perini" class="form-control-label">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>Periodo inicial
                            </label>
                            <input
                                type="text"
                                id="perini"
                                name="perini"
                                date="month"
                                class="form-control"
                                placeholder="Periodo inicial"
                                value="{{ date('Ym', strtotime('-3 month')) }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <div class="form-group mb-0">
                            <label for="perfin" class="form-control-label">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>Periodo final
                            </label>
                            <input
                                type="text"
                                id="perfin"
                                name="perfin"
                                date="month"
                                class="form-control"
                                placeholder="Periodo final"
                                value="{{ date('Ym') }}">
                        </div>
                    </div>
                </div>
            </form>
            <div id="consulta"></div>
        </div>
    </div>
</div>
@endsection
