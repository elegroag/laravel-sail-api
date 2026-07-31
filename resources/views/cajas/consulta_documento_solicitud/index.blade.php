@extends('layouts.cajas')

@push('styles')
    <style>
        .cds-page-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
        }

        .cds-page-card .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #ffffff;
            padding: 1.1rem 1.35rem;
        }

        .cds-page-card .card-header h2,
        .cds-page-card .card-header p {
            color: #ffffff !important;
            margin: 0;
        }

        .cds-page-card .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .cds-page-card .card-header p {
            margin-top: 0.35rem;
            opacity: 0.95;
            font-size: 0.875rem;
        }

        .cds-info {
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

        .cds-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }

        .cds-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.1rem 0.35rem;
            margin-bottom: 1rem;
        }

        .cds-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .cds-filters .form-control-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .cds-filters .form-text {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .cds-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0.85rem;
            margin-top: 0.65rem;
            padding-top: 0.35rem;
        }

        .cds-actions .btn {
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

        .cds-actions .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        .cds-actions .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            box-shadow: none;
            transform: none;
        }

        .cds-actions .btn-primary {
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 100%);
            border-color: #0a58ca;
        }

        .cds-results {
            margin-top: 1.25rem;
            display: none;
        }

        .cds-results.is-visible {
            display: block;
        }

        .cds-results-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .cds-results-meta strong {
            color: #0f172a;
        }

        .cds-groups {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }

        @media (min-width: 992px) {
            .cds-groups {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .cds-tipo-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(15, 23, 42, 0.05);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            overflow: hidden;
        }

        .cds-tipo-card__header {
            align-items: center;
            background: #e8eef7;
            border-bottom: 1px solid #c5d4e8;
            display: flex;
            gap: 0.75rem;
            justify-content: space-between;
            padding: 0.85rem 1rem;
        }

        .cds-tipo-card__title {
            color: #1e3a5f;
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0;
        }

        .cds-tipo-card__meta {
            color: #64748b;
            font-size: 0.75rem;
            margin: 0.2rem 0 0;
        }

        .cds-tipo-card__badge {
            background: #dbeafe;
            border-radius: 999px;
            color: #1e40af;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            white-space: nowrap;
        }

        .cds-tipo-card__body {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            padding: 0.85rem 1rem 1rem;
        }

        .cds-solicitud {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 0.85rem;
        }

        .cds-solicitud__top {
            align-items: flex-start;
            display: flex;
            gap: 0.5rem;
            justify-content: space-between;
            margin-bottom: 0.45rem;
        }

        .cds-solicitud__nombre {
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1.35;
            margin: 0;
        }

        .cds-solicitud__ruuid {
            color: #64748b;
            font-size: 0.75rem;
            margin: 0.15rem 0 0;
        }

        .cds-estado {
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.2;
            padding: 0.2rem 0.55rem;
            white-space: nowrap;
        }

        .cds-estado--t { background: #e7f1ff; color: #0d6efd; }
        .cds-estado--d { background: #cff4fc; color: #055160; }
        .cds-estado--a { background: #d1e7dd; color: #0f5132; }
        .cds-estado--p { background: #fff3cd; color: #664d03; }
        .cds-estado--x { background: #e2e3e5; color: #41464b; }
        .cds-estado--c,
        .cds-estado--i { background: #f8d7da; color: #842029; }

        .cds-solicitud__fechas {
            color: #64748b;
            display: grid;
            gap: 0.25rem;
            font-size: 0.8rem;
            margin: 0;
        }

        .cds-solicitud__fechas i {
            margin-right: 0.25rem;
            width: 0.9rem;
        }

        .cds-empty {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            color: #64748b;
            font-size: 0.92rem;
            padding: 1.5rem;
            text-align: center;
        }
    </style>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="card cds-page-card">
                <div class="card-header text-white">
                    <h2 class="text-white mb-0">{{ $title }}</h2>
                    <p class="text-white mb-0">Consulte en HTML las solicitudes de afiliación asociadas a un documento de identificación.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="cds-info" role="note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            Use el <strong>documento de identificación</strong> de la persona.
                            La consulta revisa empresa (<code>cedrep</code>), trabajador (<code>cedtra</code>),
                            cónyuge (<code>cedcon</code>), beneficiario (<code>numdoc</code>),
                            facultativo, pensionado e independiente (<code>cedtra</code>).
                            Solo se muestran los tipos con coincidencias.
                        </div>
                    </div>

                    <form id="form-cds" autocomplete="off" novalidate>
                        @csrf
                        <div class="cds-filters">
                            <div class="cds-section-title">Identificación de la persona</div>
                            <div class="row">
                                <div class="col-md-5 col-lg-4">
                                    <div class="form-group">
                                        <label class="form-control-label" for="documento">
                                            Documento de identificación <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               id="documento"
                                               name="documento"
                                               maxlength="20"
                                               placeholder="Ej. 1048123456"
                                               required
                                               aria-required="true"
                                               autocomplete="off">
                                        <small class="form-text">Número de documento sin puntos ni espacios.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cds-actions">
                            <button type="submit" id="btn-consultar" class="btn btn-primary">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <span data-role="btn-label">Consultar</span>
                            </button>
                            <button type="button" id="btn-limpiar" class="btn btn-outline-secondary">
                                <i class="fas fa-eraser" aria-hidden="true"></i>
                                Limpiar
                            </button>
                        </div>
                    </form>

                    <div id="cds-results" class="cds-results" aria-live="polite">
                        <div class="cds-results-meta">
                            <div id="cds-summary"></div>
                        </div>
                        <div id="cds-empty" class="cds-empty" style="display: none;">
                            No se encontraron solicitudes para el documento indicado.
                        </div>
                        <div id="cds-groups" class="cds-groups"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.ServerController = 'consulta-documento-solicitud';
    window.ConsultaDocumentoSolicitudRoutes = {
        consultar: @json(route('cajas.consulta-documento-solicitud.consultar')),
    };
</script>
<script src="{{ versioned_asset('cajas/build/ConsultaDocumentoSolicitud.js') }}"></script>
@endpush
