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
        background: linear-gradient(145deg, #e7f1ff 0%, #ffffff 100%);
        border-color: rgba(13, 110, 253, 0.22);
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
        font-size: 1.35rem;
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
        width: 100% !important;
        table-layout: fixed;
    }

    .consulta-data-table thead th,
    .consulta-data-table tbody td {
        padding: 0.7rem 0.65rem;
        vertical-align: middle;
        box-sizing: border-box;
        word-wrap: break-word;
        overflow-wrap: anywhere;
    }

    .consulta-data-table thead th {
        background: #f1f5f9;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 0;
        white-space: nowrap;
    }

    .consulta-data-table tbody td {
        color: #334155;
    }

    .consulta-data-table tbody tr:hover {
        background: #f8fbff;
    }

    .consulta-data-table th:nth-child(1),
    .consulta-data-table td:nth-child(1) {
        width: 12%;
    }

    .consulta-data-table th:nth-child(2),
    .consulta-data-table td:nth-child(2) {
        width: 10%;
    }

    .consulta-data-table th:nth-child(3),
    .consulta-data-table td:nth-child(3),
    .consulta-data-table th:nth-child(4),
    .consulta-data-table td:nth-child(4) {
        width: 20%;
    }

    .consulta-data-table th:nth-child(5),
    .consulta-data-table td:nth-child(5) {
        width: 12%;
    }

    .consulta-data-table th:nth-child(6),
    .consulta-data-table td:nth-child(6) {
        width: 10%;
        text-align: end;
    }

    .consulta-data-table th:nth-child(7),
    .consulta-data-table td:nth-child(7) {
        width: 16%;
        text-align: end;
    }

    .consulta-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: #e7f1ff;
        color: #0d6efd;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .consulta-money {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: #198754;
        white-space: nowrap;
    }

    .consulta-name {
        min-width: 160px;
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
        min-width: 0;
    }

    #consulta .table-responsive {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }

    #consulta .dataTables_wrapper {
        width: 100%;
        min-width: 0;
    }

    #consulta .dataTables_wrapper .consulta-data-table {
        width: 100% !important;
        margin: 0 !important;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'subsidio';
    </script>
    <script src="{{ versioned_asset('mercurio/build/ConsultasTrabajador.js') }}"></script>
@endpush

@section('content')
<script type="text/template" id="templateConsulta">
<%
    var totalCuotas = cuotas.reduce(function (total, item) {
        return total + Number(item.numcuo || 0);
    }, 0);
    var totalValor = cuotas.reduce(function (total, item) {
        return total + Number(item.valor || 0);
    }, 0);
    var formatMoney = function (value) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            maximumFractionDigits: 0,
        }).format(Number(value || 0));
    };
%>

<div class="consulta-results">
    <div class="consulta-summary-grid">
        <div class="consulta-summary-card">
            <span class="consulta-summary-label">Número de cuotas</span>
            <strong class="consulta-summary-value"><%= totalCuotas %></strong>
        </div>
        <div class="consulta-summary-card consulta-summary-card-accent">
            <span class="consulta-summary-label">Valor neto total</span>
            <strong class="consulta-summary-value"><%= formatMoney(totalValor) %></strong>
        </div>
        <div class="consulta-summary-card">
            <span class="consulta-summary-label">Registros encontrados</span>
            <strong class="consulta-summary-value"><%= cuotas.length %></strong>
        </div>
    </div>

    <% if (cuotas.length === 0) { %>
        <div class="consulta-empty-state">
            <i class="fas fa-inbox" aria-hidden="true"></i>
            <strong>No hay datos para mostrar</strong>
            <span>Ajuste el rango de periodos e intente nuevamente.</span>
        </div>
    <% } else { %>
        <div class="table-responsive">
            <table id="dataTable" class="table table-hover consulta-data-table align-middle w-100">
                <thead>
                    <tr>
                        <th scope="col">Periodo girado</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Nombre responsable</th>
                        <th scope="col">Nombre beneficiario</th>
                        <th scope="col">Forma pago</th>
                        <th scope="col">Número cuotas</th>
                        <th scope="col">Valor neto</th>
                    </tr>
                </thead>
                <tbody>
                    <% _.each(cuotas, function(item) { %>
                        <tr>
                            <td><%= item.pergir %></td>
                            <td><span class="consulta-badge"><%= item.tipo_pago %></span></td>
                            <td class="consulta-name"><%= item.nomres %></td>
                            <td class="consulta-name"><%= item.nombre %></td>
                            <td><%= item.tippag %></td>
                            <td><%= item.numcuo %></td>
                            <td class="consulta-money"><%= formatMoney(item.valor) %></td>
                        </tr>
                    <% }); %>
                </tbody>
            </table>
        </div>
        <% } %>
</div>
</script>

<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 consulta-page-card">
        <div class="card-header border-0 pb-2 pb-md-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h2 class="h5 mb-1">{{ $title ?? 'Consulta de giro' }}</h2>
                    <p class="mb-0 text-sm text-muted">
                        Consulte los giros de cuota monetaria por rango de periodos.
                    </p>
                </div>
                <div class="text-md-end">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2"
                        id="bt_consulta_giro">
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
