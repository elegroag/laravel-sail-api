@extends('layouts.bone')

@push('styles')
    <style>
        .historial-page-card {
            border-radius: 16px;
            overflow: hidden;
        }

        .historial-page-header {
            padding: 1.25rem 1.5rem 0.5rem;
            border-bottom: 1px solid #eef2f7;
        }

        .historial-page-title {
            margin: 0 0 0.35rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: #334155;
        }

        .historial-page-subtitle {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.55;
        }

        .historial-tabs-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.35rem;
        }

        .historial-tabs {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.65rem;
            padding: 0;
            margin: 0;
            background: transparent;
            border: 0;
            max-width: 100%;
        }

        .historial-tabs .nav-item {
            margin: 0;
        }

        .historial-tabs .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            min-width: 10rem;
            border-radius: 12px;
            padding: 0.7rem 1.1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .historial-tabs .nav-link i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.65rem;
            height: 1.65rem;
            border-radius: 999px;
            background: #eef2f7;
            color: #64748b;
            font-size: 0.78rem;
            transition: all 0.2s ease;
        }

        .historial-tabs .nav-link:hover {
            color: #0d6efd;
            background: #fff;
            border-color: rgba(13, 110, 253, 0.18);
            transform: translateY(-1px);
        }

        .historial-tabs .nav-link:hover i {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .historial-tabs .nav-link.active {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.28);
        }

        .historial-tabs .nav-link.active i {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .historial-tab-content {
            padding-top: 0.25rem;
        }

        .historial-table-wrap {
            border: 1px solid #e9ecef;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }

        .historial-data-table {
            font-size: 0.84rem;
            margin-bottom: 0;
        }

        .historial-data-table thead th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 0;
            padding: 0.75rem 0.85rem;
            white-space: nowrap;
        }

        .historial-data-table tbody td {
            padding: 0.75rem 0.85rem;
            vertical-align: middle;
            color: #334155;
            border-color: #eef2f7;
        }

        .historial-data-table tbody tr:hover {
            background: #f8fbff;
        }

        .historial-empty-state {
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

        .historial-empty-state i {
            font-size: 1.75rem;
            color: #adb5bd;
        }

        .historial-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #eef2f7;
            color: #475569;
        }

        @media (max-width: 991.98px) {
            .historial-tabs-wrap {
                justify-content: stretch;
            }

            .historial-tabs {
                width: 100%;
            }

            .historial-tabs .nav-item {
                flex: 1 1 calc(50% - 0.35rem);
            }

            .historial-tabs .nav-link {
                width: 100%;
                min-width: 0;
                padding: 0.65rem 0.75rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 historial-page-card">
        <div class="card-header border-0 historial-page-header">
            <div>
                <h2 class="historial-page-title">{{ $title ?? 'Historial de cuenta' }}</h2>
                <p class="historial-page-subtitle">
                    Consulta el historial de actualizaciones de datos básicos, afiliaciones y certificados asociados a tu cuenta.
                </p>
            </div>
        </div>

        <div class="card-body pt-2 px-3 px-md-4 pb-4">
            <div class="historial-tabs-wrap">
                <ul class="nav nav-pills historial-tabs" id="tabs-icons-text" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active"
                           id="tabs-icons-text-1-tab"
                           data-bs-toggle="tab"
                           href="#tabs-icons-text-1"
                           role="tab"
                           aria-controls="tabs-icons-text-1"
                           aria-selected="true">
                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                            <span>Datos básicos</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"
                           id="tabs-icons-text-2-tab"
                           data-bs-toggle="tab"
                           href="#tabs-icons-text-2"
                           role="tab"
                           aria-controls="tabs-icons-text-2"
                           aria-selected="false">
                            <i class="fas fa-child" aria-hidden="true"></i>
                            <span>Afiliación beneficiario</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"
                           id="tabs-icons-text-3-tab"
                           data-bs-toggle="tab"
                           href="#tabs-icons-text-3"
                           role="tab"
                           aria-controls="tabs-icons-text-3"
                           aria-selected="false">
                            <i class="fas fa-user-friends" aria-hidden="true"></i>
                            <span>Afiliación cónyuges</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"
                           id="tabs-icons-text-4-tab"
                           data-bs-toggle="tab"
                           href="#tabs-icons-text-4"
                           role="tab"
                           aria-controls="tabs-icons-text-4"
                           aria-selected="false">
                            <i class="fas fa-certificate" aria-hidden="true"></i>
                            <span>Certificados</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content historial-tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    @include('mercurio.subsidio.tmp.actualizacion_basico', ['items' => $mercurio33])
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                    @include('mercurio.subsidio.tmp.afiliacion_beneficiario', ['items' => $mercurio34])
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                    @include('mercurio.subsidio.tmp.afiliacion_conyuge', ['items' => $mercurio32])
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-4" role="tabpanel" aria-labelledby="tabs-icons-text-4-tab">
                    @include('mercurio.subsidio.tmp.certificados', ['items' => $mercurio45])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ versioned_asset('mercurio/build/ConsultasTrabajador.js') }}"></script>
@endpush
