@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <style>
        .nucleo-page-card {
            border-radius: 16px;
        }

        .nucleo-tabs-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.35rem;
        }

        .nucleo-tabs {
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

        .nucleo-tabs .nav-item {
            margin: 0;
        }

        .nucleo-tabs .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            min-width: 9.5rem;
            border-radius: 12px;
            padding: 0.7rem 1.15rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .nucleo-tabs .nav-link i {
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

        .nucleo-tabs .nav-link:hover {
            color: #0d6efd;
            background: #fff;
            border-color: rgba(13, 110, 253, 0.18);
            transform: translateY(-1px);
        }

        .nucleo-tabs .nav-link:hover i {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .nucleo-tabs .nav-link.active {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.28);
        }

        .nucleo-tabs .nav-link.active i {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .nucleo-tabs .nav-link:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }

        .nucleo-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .nucleo-summary-card {
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid rgba(13, 110, 253, 0.12);
            border-radius: 14px;
            padding: 1rem 1.15rem;
        }

        .nucleo-summary-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .nucleo-summary-value {
            display: block;
            font-size: 1rem;
            line-height: 1.35;
            color: #212529;
            word-break: break-word;
        }

        .nucleo-section {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 1.15rem 1.25rem 1.25rem;
            margin-bottom: 1rem;
        }

        .nucleo-section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eef2f7;
        }

        .nucleo-section-header h3 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: #334155;
        }

        .nucleo-section-header i {
            color: #0d6efd;
        }

        .nucleo-field-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.85rem 1rem;
        }

        .nucleo-field {
            min-width: 0;
            padding: 0.75rem 0.85rem;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
        }

        .nucleo-field-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }

        .nucleo-field-value {
            display: block;
            font-size: 0.92rem;
            line-height: 1.45;
            color: #1e293b;
            word-break: break-word;
        }

        .nucleo-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.18rem 0.55rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .nucleo-badge-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .nucleo-badge-muted {
            background: #e9ecef;
            color: #495057;
        }

        .nucleo-member-card {
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 1.15rem 1.25rem;
            margin-bottom: 1rem;
            background: #fff;
        }

        .nucleo-member-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eef2f7;
        }

        .nucleo-member-title h3 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: #334155;
        }

        .nucleo-empty-state {
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

        .nucleo-empty-state i {
            font-size: 1.75rem;
            color: #adb5bd;
        }

        @media (max-width: 767.98px) {
            .nucleo-tabs-wrap {
                justify-content: stretch;
            }

            .nucleo-tabs {
                width: 100%;
            }

            .nucleo-tabs .nav-item {
                flex: 1 1 calc(50% - 0.35rem);
            }

            .nucleo-tabs .nav-link {
                width: 100%;
                min-width: 0;
                padding: 0.65rem 0.75rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 nucleo-page-card">
        <div class="card-header border-0 pb-2">
            <div>
                <h2 class="h5 mb-1">{{ $title ?? 'Consulta núcleo familiar' }}</h2>
                <p class="mb-0 text-sm text-muted">
                    Consulta y gestiona la información del trabajador, cónyuge y beneficiarios en pestañas separadas.
                </p>
            </div>
        </div>
        <div class="card-body pt-2">
            <div class="nucleo-tabs-wrap">
                <ul class="nav nav-pills nucleo-tabs" id="tabs-icons-text" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active"
                           id="tabsTrabajadorTab"
                           data-bs-toggle="tab"
                           href="#tabsTrabajador"
                           role="tab"
                           aria-controls="tabsTrabajador"
                           aria-selected="true">
                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                            <span>Trabajador</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"
                           id="tabsConyugeTab"
                           data-bs-toggle="tab"
                           href="#tabsConyuge"
                           role="tab"
                           aria-controls="tabsConyuge"
                           aria-selected="false">
                            <i class="fas fa-user-friends" aria-hidden="true"></i>
                            <span>Cónyuges</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"
                           id="tabsBeneficiarioTab"
                           data-bs-toggle="tab"
                           href="#tabsBeneficiario"
                           role="tab"
                           aria-controls="tabsBeneficiario"
                           aria-selected="false">
                            <i class="fas fa-child" aria-hidden="true"></i>
                            <span>Beneficiarios</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div id="myTabContent" class="tab-content"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script type="text/template" id="tmp_layout">
        <div
            class="tab-pane fade show active"
            id="tabsTrabajador"
            role="tabpanel"
            aria-labelledby="tabsTrabajadorTab">
        </div>

        <div
            class="tab-pane fade"
            id="tabsConyuge"
            role="tabpanel"
            aria-labelledby="tabsConyugeTab">
        </div>

        <div
            class="tab-pane fade"
            id="tabsBeneficiario"
            role="tabpanel"
            aria-labelledby="tabsBeneficiarioTab">
        </div>
    </script>

    <script type="text/template" id="templateTrabajador">
        @include('mercurio/subsidio/tmp/tmp_nucleo')
    </script>

    <script type="text/template" id="templateConyuge">
        @include('mercurio/subsidio/tmp/tmp_conyuge')
    </script>

    <script type="text/template" id="templateBeneficiario">
        @include('mercurio/subsidio/tmp/tmp_beneficiario')
    </script>

    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'subsidio';
    </script>

    <script src="{{ versioned_asset('mercurio/build/ConsultaNucleo.js') }}"></script>
@endpush
