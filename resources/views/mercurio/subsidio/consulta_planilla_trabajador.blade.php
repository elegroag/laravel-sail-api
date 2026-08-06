@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <style>
        .consulta-page-card {
            border-radius: 16px;
        }

        .consulta-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
            font-size: 1.2rem;
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

        .consulta-list-company {
            font-size: 0.98rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .consulta-list-period {
            margin-top: 0.25rem;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .consulta-list-fields {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.55rem;
            margin-bottom: 0.85rem;
        }

        @media (min-width: 768px) {
            .consulta-list-fields {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
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

        .consulta-money {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
            color: #198754;
            white-space: nowrap;
            font-size: 1rem;
        }

        .consulta-novedades {
            border-top: 1px solid #e2e8f0;
            padding-top: 0.75rem;
        }

        .consulta-novedades-title {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .consulta-novedades-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .consulta-flag {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            background: #e7f1ff;
            color: #0d6efd;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .consulta-flag-muted {
            color: #94a3b8;
            background: #f1f5f9;
        }

        .consulta-flag-code {
            opacity: 0.85;
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
    <script type="text/template" id="templatePlanillas">
    <%
        var formatMoney = function (value) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0,
            }).format(Number(value || 0));
        };
        var totalDias = planilla.reduce(function (total, item) {
            return total + Number(item.diatra || 0);
        }, 0);
        var promedioSalario = planilla.length
            ? planilla.reduce(function (total, item) {
                return total + Number(item.salbas || 0);
            }, 0) / planilla.length
            : 0;
        var flagText = function (value) {
            var text = (value || '').toString().trim();
            if (!text || text === '0' || text === '00') {
                return '-';
            }
            return text;
        };
        var flagIsMuted = function (value) {
            var text = (value || '').toString().trim();
            return !text || text === '0' || text === '00';
        };
        var escapeAttr = function (value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        };
        var novedades = [
            { key: 'ingtra', code: 'Ing', title: 'Ingreso' },
            { key: 'novret', code: 'Ret', title: 'Retiro' },
            { key: 'novvps', code: 'VST', title: 'Variación salarial temporal' },
            { key: 'novstc', code: 'STC', title: 'Suspensión temporal contrato' },
            { key: 'novitg', code: 'ITE', title: 'Incapacidad temporal enfermedad' },
            { key: 'licnom', code: 'LM', title: 'Licencia maternidad' },
            { key: 'vacnom', code: 'VAC', title: 'Vacaciones' },
            { key: 'incnom', code: 'ITAT', title: 'Incapacidad temporal accidente de trabajo' },
            { key: 'novvts', code: 'VSP', title: 'Variación salarial permanente' }
        ];
    %>

    <div class="consulta-results">
        <div class="consulta-summary-grid">
            <div class="consulta-summary-card">
                <span class="consulta-summary-label">Registros</span>
                <strong class="consulta-summary-value"><%= planilla.length %></strong>
            </div>
            <div class="consulta-summary-card consulta-summary-card-accent">
                <span class="consulta-summary-label">Salario base promedio</span>
                <strong class="consulta-summary-value"><%= formatMoney(promedioSalario) %></strong>
            </div>
            <div class="consulta-summary-card">
                <span class="consulta-summary-label">Días trabajados</span>
                <strong class="consulta-summary-value"><%= totalDias %></strong>
            </div>
        </div>

        <% if (planilla.length === 0) { %>
            <div class="consulta-empty-state">
                <i class="fas fa-file-invoice" aria-hidden="true"></i>
                <strong>No hay planillas para mostrar</strong>
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
                        placeholder="Empresa, periodo, salario..."
                        autocomplete="off">
                </label>
            </div>

            <ul class="consulta-list" id="consultaList">
                <% _.each(planilla, function (item) {
                    var searchText = [
                        item.razsoc,
                        item.perapo,
                        item.tarapo,
                        item.salbas,
                        item.diatra,
                        item.fecrec,
                        item.ingtra,
                        item.novret,
                        item.novvps,
                        item.novstc,
                        item.novitg,
                        item.licnom,
                        item.vacnom,
                        item.incnom,
                        item.novvts
                    ].join(' ').toLowerCase();
                %>
                    <li class="consulta-list-item" data-search="<%= escapeAttr(searchText) %>">
                        <div class="consulta-list-item-header">
                            <div>
                                <div class="consulta-list-company"><%= item.razsoc || '—' %></div>
                                <div class="consulta-list-period">Periodo aporte <%= item.perapo || '—' %></div>
                            </div>
                            <div class="consulta-money"><%= formatMoney(item.salbas) %></div>
                        </div>

                        <div class="consulta-list-fields">
                            <div class="consulta-list-field">
                                <span class="consulta-list-field-label">Índice aporte</span>
                                <span class="consulta-list-field-value"><%= item.tarapo || '—' %></span>
                            </div>
                            <div class="consulta-list-field">
                                <span class="consulta-list-field-label">Días trabajados</span>
                                <span class="consulta-list-field-value"><%= item.diatra || 0 %></span>
                            </div>
                            <div class="consulta-list-field">
                                <span class="consulta-list-field-label">Fecha pago</span>
                                <span class="consulta-list-field-value"><%= item.fecrec || '—' %></span>
                            </div>
                        </div>

                        <div class="consulta-novedades">
                            <span class="consulta-novedades-title">Novedades</span>
                            <div class="consulta-novedades-grid">
                                <% _.each(novedades, function (nov) {
                                    var muted = flagIsMuted(item[nov.key]);
                                %>
                                    <span
                                        class="consulta-flag <%= muted ? 'consulta-flag-muted' : '' %>"
                                        title="<%= nov.title %>">
                                        <span class="consulta-flag-code"><%= nov.code %></span>
                                        <span><%= flagText(item[nov.key]) %></span>
                                    </span>
                                <% }); %>
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
                    <h2 class="h5 mb-1">{{ $title ?? 'Planillas Pila' }}</h2>
                    <p class="mb-0 text-sm text-muted">
                        Consulta las planillas de aportes del trabajador por periodo de pago.
                    </p>
                </div>
                <div class="text-md-end">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2"
                        id="bt_consulta_planilla_trabajador">
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
