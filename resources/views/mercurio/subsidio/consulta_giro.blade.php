@extends('layouts.bone')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
<style>
    .consulta-page-card {
        border-radius: 16px;
    }

    .consulta-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
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
        font-size: 1.25rem;
        line-height: 1.2;
        color: #212529;
        word-break: break-word;
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

    .consulta-list-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .consulta-list-toolbar label {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-bottom: 0;
        font-size: 0.875rem;
        color: #64748b;
    }

    .consulta-list-toolbar select,
    .consulta-list-toolbar input[type='search'] {
        min-height: 38px;
        border-radius: 8px;
    }

    .consulta-list-toolbar input[type='search'] {
        width: min(100%, 240px);
    }

    .consulta-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .consulta-list-item {
        margin: 0;
        padding: 1rem 1.1rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .consulta-list-item[hidden] {
        display: none !important;
    }

    .consulta-list-item-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .consulta-list-period {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: 0.02em;
    }

    .consulta-list-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.4rem;
    }

    .consulta-list-fields {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.55rem;
    }

    @media (min-width: 768px) {
        .consulta-list-fields {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .consulta-list-field {
        min-width: 0;
    }

    .consulta-list-field-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #94a3b8;
        margin-bottom: 0.15rem;
    }

    .consulta-list-field-value {
        display: block;
        font-size: 0.92rem;
        color: #334155;
        line-height: 1.35;
        overflow-wrap: anywhere;
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
        font-weight: 700;
        color: #198754;
        white-space: nowrap;
        font-size: 1rem;
    }

    .consulta-list-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .consulta-list-pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .consulta-list-pagination button {
        min-width: 36px;
        height: 36px;
        padding: 0 0.65rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        font-size: 0.875rem;
    }

    .consulta-list-pagination button.is-active,
    .consulta-list-pagination button:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .consulta-list-pagination button:disabled {
        opacity: 0.45;
        pointer-events: none;
    }

    #consulta {
        max-width: 100%;
        min-width: 0;
    }
</style>
@endpush

@push('scripts')
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
    var escapeAttr = function (value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
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
        <div class="consulta-list-toolbar">
            <label>
                Mostrar
                <select id="consultaPageLength" class="form-select form-select-sm" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                por página
            </label>
            <label>
                Buscar
                <input
                    type="search"
                    id="consultaSearch"
                    class="form-control form-control-sm"
                    placeholder="Periodo, nombre, pago..."
                    autocomplete="off">
            </label>
        </div>

        <ul class="consulta-list" id="consultaList">
            <% _.each(cuotas, function(item) {
                var searchText = [
                    item.pergir,
                    item.tipo_pago,
                    item.nomres,
                    item.nombre,
                    item.tippag,
                    item.numcuo,
                    item.valor
                ].join(' ').toLowerCase();
            %>
                <li class="consulta-list-item" data-search="<%= escapeAttr(searchText) %>">
                    <div class="consulta-list-item-header">
                        <div>
                            <div class="consulta-list-period"><%= item.pergir %></div>
                            <div class="consulta-list-meta">
                                <span class="consulta-badge"><%= item.tipo_pago %></span>
                            </div>
                        </div>
                        <div class="consulta-money"><%= formatMoney(item.valor) %></div>
                    </div>
                    <div class="consulta-list-fields">
                        <div class="consulta-list-field">
                            <span class="consulta-list-field-label">Nombre responsable</span>
                            <span class="consulta-list-field-value"><%= item.nomres || '—' %></span>
                        </div>
                        <div class="consulta-list-field">
                            <span class="consulta-list-field-label">Nombre beneficiario</span>
                            <span class="consulta-list-field-value"><%= item.nombre || '—' %></span>
                        </div>
                        <div class="consulta-list-field">
                            <span class="consulta-list-field-label">Forma de pago</span>
                            <span class="consulta-list-field-value"><%= item.tippag || '—' %></span>
                        </div>
                        <div class="consulta-list-field">
                            <span class="consulta-list-field-label">Número de cuotas</span>
                            <span class="consulta-list-field-value"><%= item.numcuo || 0 %></span>
                        </div>
                    </div>
                </li>
            <% }); %>
        </ul>

        <div class="consulta-list-footer">
            <div id="consultaListInfo">Mostrando 0 registros</div>
            <ul class="consulta-list-pagination" id="consultaListPagination"></ul>
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
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
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
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
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
