@extends('layouts.cajas')

@php
    $cargaPorAsesor = collect($gener02)->map(function ($asesor) use ($mercurio09) {
        $items = collect($mercurio09)
            ->filter(fn ($m09) => (string) $m09['usuario'] === (string) $asesor->usuario)
            ->sortByDesc('cantidad')
            ->values();

        return [
            'usuario' => $asesor->usuario,
            'nombre' => $asesor->getNombre(),
            'login' => $asesor->login ?? $asesor->getLogin(),
            'items' => $items,
            'total' => $items->sum('cantidad'),
            'tipos' => $items->count(),
        ];
    })->sortByDesc('total')->values();

    $totalPendientes = $cargaPorAsesor->sum('total');
    $totalAsesores = $cargaPorAsesor->count();
    $asesoresConCarga = $cargaPorAsesor->where('total', '>', 0)->count();
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <style>
        .carga-page-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.1);
        }

        .carga-page-card > .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #fff;
            padding: 1.1rem 1.35rem;
        }

        .carga-page-card > .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: #fff;
        }

        .carga-page-card > .card-header p {
            margin: 0.35rem 0 0;
            opacity: 0.95;
            font-size: 0.875rem;
            color: #fff;
        }

        .carga-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 767.98px) {
            .carga-summary {
                grid-template-columns: 1fr;
            }
        }

        .carga-summary-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.9rem 1rem;
        }

        .carga-summary-item .label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .carga-summary-item .value {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .carga-info {
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

        .carga-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }

        .carga-asesor-card {
            border: 0.5px solid #cbd5e1 !important;
            border-radius: 12px;
            box-shadow: none;
            height: 100%;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .carga-asesor-card:hover {
            border-color: #93c5fd !important;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        }

        .carga-asesor-card .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.1rem;
        }

        .carga-asesor-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .carga-avatar {
            flex: 0 0 auto;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .carga-asesor-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.25;
            word-break: break-word;
        }

        .carga-asesor-login {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.1rem;
        }

        .carga-total-badge {
            flex: 0 0 auto;
            min-width: 3rem;
            text-align: center;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 0.35rem 0.55rem;
            line-height: 1.1;
        }

        .carga-total-badge.is-empty {
            background: #f8fafc;
            color: #94a3b8;
            border-color: #e2e8f0;
        }

        .carga-total-badge .num {
            display: block;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .carga-total-badge .lbl {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .carga-asesor-card .card-body {
            padding: 0.35rem 0.85rem 0.85rem;
        }

        .carga-tipopc-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .carga-tipopc-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.25rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .carga-tipopc-list li.is-clickable {
            cursor: pointer;
            border-radius: 8px;
            padding-left: 0.45rem;
            padding-right: 0.45rem;
            transition: background-color 0.15s ease;
        }

        .carga-tipopc-list li.is-clickable:hover {
            background: #f0f7ff;
        }

        .carga-tipopc-list li.is-clickable:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 1px;
        }

        .carga-tipopc-list li:last-child {
            border-bottom: 0;
        }

        .carga-tipopc-list .detalle {
            font-size: 0.84rem;
            color: #334155;
            line-height: 1.3;
        }

        .carga-tipopc-list .detalle-wrap {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            min-width: 0;
        }

        .carga-tipopc-list .detalle-wrap i {
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .carga-tipopc-list li.is-clickable .detalle-wrap i {
            color: #3b82f6;
        }

        .carga-tipopc-list .cantidad {
            flex: 0 0 auto;
            min-width: 2rem;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.25rem 0.55rem;
            background: #dbeafe;
            color: #1e40af;
        }

        .carga-tipopc-list .cantidad.is-zero {
            background: #f1f5f9;
            color: #94a3b8;
            font-weight: 600;
        }

        .carga-tipopc-list .cantidad.is-high {
            background: #fee2e2;
            color: #b91c1c;
        }

        .carga-empty {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            padding: 1.25rem 0.5rem;
        }

        .carga-solicitudes-table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
            background: #f8fafc;
            white-space: nowrap;
        }

        .carga-solicitudes-table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        #cargaLaboralModal .modal-dialog {
            max-width: 920px;
        }

        #cargaLaboralModal .modal-header {
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            border: 0;
        }

        #cargaLaboralModal .modal-header .btn-close {
            filter: invert(1);
        }

        #cargaLaboralModal .modal-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            margin: 0.2rem 0 0;
        }

        #cargaLaboralModal .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0.75rem;
        }

        #cargaLaboralModal .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5rem;
            min-width: 220px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.35rem 0.65rem;
        }

        #cargaLaboralModal .dataTables_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.25rem 0.5rem;
            margin: 0 0.35rem;
        }

        #cargaLaboralModal .dataTables_wrapper .dataTables_info,
        #cargaLaboralModal .dataTables_wrapper .dataTables_paginate {
            margin-top: 0.75rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid mt-3 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card carga-page-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h2>Carga laboral por asesor</h2>
                        <p>Solicitudes pendientes (estado P) agrupadas por tipo de operación.</p>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="carga-info">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            Cada tarjeta corresponde a un asesor activo. Los conteos incluyen únicamente solicitudes en estado <strong>pendiente</strong>.
                            Haz clic en un tipopc con pendientes para ver el detalle en tabla.
                        </div>
                    </div>

                    <div class="carga-summary">
                        <div class="carga-summary-item">
                            <span class="label">Asesores</span>
                            <span class="value">{{ $totalAsesores }}</span>
                        </div>
                        <div class="carga-summary-item">
                            <span class="label">Asesores con pendientes</span>
                            <span class="value">{{ $asesoresConCarga }}</span>
                        </div>
                        <div class="carga-summary-item">
                            <span class="label">Total solicitudes pendientes</span>
                            <span class="value">{{ $totalPendientes }}</span>
                        </div>
                    </div>

                    @if ($cargaPorAsesor->isEmpty())
                        <div class="carga-empty">
                            No hay asesores activos con tipopc asignados.
                        </div>
                    @else
                        <div class="row">
                            @foreach ($cargaPorAsesor as $asesor)
                                @php
                                    $iniciales = collect(preg_split('/\s+/', trim($asesor['nombre'])))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
                                        ->implode('');
                                @endphp
                                <div class="col-md-6 col-xl-4 mb-3">
                                    <div class="card carga-asesor-card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                <div class="carga-asesor-meta">
                                                    <span class="carga-avatar" aria-hidden="true">{{ $iniciales ?: 'AS' }}</span>
                                                    <div>
                                                        <h6 class="carga-asesor-name">{{ $asesor['nombre'] }}</h6>
                                                        <span class="carga-asesor-login">
                                                            Código: {{ $asesor['usuario'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="carga-total-badge {{ $asesor['total'] == 0 ? 'is-empty' : '' }}" title="Total pendientes">
                                                    <span class="num">{{ $asesor['total'] }}</span>
                                                    <span class="lbl">total</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if ($asesor['items']->isEmpty())
                                                <div class="carga-empty mb-0">Sin tipopc asignados</div>
                                            @else
                                                <ul class="carga-tipopc-list">
                                                    @foreach ($asesor['items'] as $item)
                                                        @php
                                                            $cantidad = (int) $item['cantidad'];
                                                            $badgeClass = $cantidad === 0
                                                                ? 'is-zero'
                                                                : ($cantidad >= 10 ? 'is-high' : '');
                                                            $puedeConsultar = $cantidad > 0;
                                                        @endphp
                                                        <li
                                                            @if ($puedeConsultar)
                                                                class="is-clickable"
                                                                role="button"
                                                                tabindex="0"
                                                                data-toggle="ver-solicitudes"
                                                                data-usuario="{{ $asesor['usuario'] }}"
                                                                data-nombre="{{ $asesor['nombre'] }}"
                                                                data-tipopc="{{ $item['tipopc'] }}"
                                                                data-detalle="{{ $item['detalle'] }}"
                                                                title="Ver solicitudes pendientes"
                                                            @endif
                                                        >
                                                            <span class="detalle-wrap">
                                                                @if ($puedeConsultar)
                                                                    <i class="fas fa-table" aria-hidden="true"></i>
                                                                @endif
                                                                <span class="detalle">{{ $item['detalle'] }}</span>
                                                            </span>
                                                            <span class="cantidad {{ $badgeClass }}">{{ $cantidad }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cargaLaboralModal" tabindex="-1" aria-labelledby="cargaLaboralModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="cargaLaboralModalLabel">Solicitudes pendientes</h5>
                    <p class="modal-subtitle" id="cargaLaboralModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="cargaLaboralModalLoading" class="text-center text-muted py-4" style="display:none;">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div>Consultando solicitudes...</div>
                </div>
                <div id="cargaLaboralModalError" class="alert alert-danger" style="display:none;"></div>
                <div id="cargaLaboralModalBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        window.ServerController = 'consulta';

        (function ($) {
            const urlSolicitudes = @json(route('consulta.solicitudesCargaLaboral'));
            const csrfToken = @json(csrf_token());
            let tablaSolicitudes = null;

            function destruirTabla() {
                if (tablaSolicitudes) {
                    tablaSolicitudes.destroy();
                    tablaSolicitudes = null;
                }
            }

            function inicializarTabla() {
                const $table = $('#tablaSolicitudesCarga');
                if (!$table.length || typeof $.fn.DataTable === 'undefined') {
                    return;
                }

                destruirTabla();
                tablaSolicitudes = $table.DataTable({
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                    order: [[0, 'desc']],
                    autoWidth: false,
                    pagingType: 'simple_numbers',
                    language: {
                        decimal: '',
                        emptyTable: 'No hay solicitudes pendientes',
                        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                        infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                        infoFiltered: '(filtrado de _MAX_ registros)',
                        lengthMenu: 'Mostrar _MENU_ registros',
                        loadingRecords: 'Cargando...',
                        processing: 'Procesando...',
                        search: 'Buscar:',
                        zeroRecords: 'No se encontraron coincidencias',
                        paginate: {
                            first: '<i class="fas fa-angle-double-left" aria-hidden="true"></i>',
                            last: '<i class="fas fa-angle-double-right" aria-hidden="true"></i>',
                            next: '<i class="fas fa-angle-right" aria-hidden="true"></i>',
                            previous: '<i class="fas fa-angle-left" aria-hidden="true"></i>',
                        },
                    },
                });
            }

            function abrirSolicitudes($el) {
                const tipopc = $el.data('tipopc');
                const usuario = $el.data('usuario');
                const detalle = $el.data('detalle');
                const nombre = $el.data('nombre');

                $('#cargaLaboralModalLabel').text(detalle || 'Solicitudes pendientes');
                $('#cargaLaboralModalSubtitle').text(nombre + ' · pendientes');
                destruirTabla();
                $('#cargaLaboralModalBody').empty();
                $('#cargaLaboralModalError').hide().text('');
                $('#cargaLaboralModalLoading').show();

                const modalEl = document.getElementById('cargaLaboralModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                $.ajax({
                    type: 'POST',
                    url: urlSolicitudes,
                    data: {
                        _token: csrfToken,
                        tipopc: tipopc,
                        usuario: usuario,
                        detalle: detalle,
                    },
                })
                    .done(function (response) {
                        $('#cargaLaboralModalLoading').hide();
                        if (!response || !response.success) {
                            $('#cargaLaboralModalError')
                                .text((response && response.msj) || 'No fue posible consultar las solicitudes.')
                                .show();
                            return;
                        }
                        $('#cargaLaboralModalSubtitle').text(
                            nombre + ' · ' + (response.count || 0) + ' pendiente(s)'
                        );
                        $('#cargaLaboralModalBody').html(response.html);
                        inicializarTabla();
                    })
                    .fail(function (jqXHR) {
                        $('#cargaLaboralModalLoading').hide();
                        const msg =
                            (jqXHR.responseJSON && jqXHR.responseJSON.msj) ||
                            'Error al consultar las solicitudes.';
                        $('#cargaLaboralModalError').text(msg).show();
                    });
            }

            $(document).on('click', '[data-toggle="ver-solicitudes"]', function (e) {
                e.preventDefault();
                abrirSolicitudes($(this));
            });

            $(document).on('keydown', '[data-toggle="ver-solicitudes"]', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    abrirSolicitudes($(this));
                }
            });

            $('#cargaLaboralModal').on('hidden.bs.modal', function () {
                destruirTabla();
                $('#cargaLaboralModalBody').empty();
            });
        })(jQuery);
    </script>
@endpush
