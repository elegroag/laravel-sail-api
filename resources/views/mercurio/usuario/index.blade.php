@extends('layouts.bone')

@push('styles')
    <style>
        .perfil-page-card {
            border-radius: 16px;
            overflow: hidden;
        }

        .perfil-page-header {
            padding: 1.25rem 1.5rem 0.5rem;
            border-bottom: 1px solid #eef2f7;
        }

        .perfil-page-title {
            margin: 0 0 0.35rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: #334155;
        }

        .perfil-page-subtitle {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.55;
        }

        .perfil-notice {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin-bottom: 1.25rem;
            padding: 0.9rem 1rem;
            border: 1px solid rgba(13, 110, 253, 0.14);
            border-radius: 12px;
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
            color: #475569;
            font-size: 0.875rem;
            line-height: 1.55;
        }

        .perfil-notice i {
            margin-top: 0.15rem;
            color: #0d6efd;
        }

        .perfil-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .perfil-summary-card {
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid rgba(13, 110, 253, 0.12);
            border-radius: 14px;
            padding: 1rem 1.15rem;
            min-height: 100%;
        }

        .perfil-summary-card.span-2 {
            grid-column: 1 / -1;
        }

        .perfil-summary-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .perfil-summary-value {
            display: block;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            word-break: break-word;
        }

        .perfil-summary-help {
            margin: 0.45rem 0 0;
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.45;
        }

        .perfil-summary-card .form-control {
            border-radius: 10px;
        }

        .perfil-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #eef2f7;
        }

        .perfil-actions .btn {
            min-width: 9rem;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.65rem 1.25rem;
        }

        .perfil-link-action {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
        }

        .perfil-link-action:hover {
            text-decoration: underline;
        }

        .perfil-password-panel {
            margin-top: 0.75rem;
            padding: 1rem;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
        }

        .perfil-password-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        @media (max-width: 767.98px) {
            .perfil-summary-grid {
                grid-template-columns: 1fr;
            }

            .perfil-summary-card.span-2 {
                grid-column: auto;
            }
        }
    </style>
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection

@push('scripts')
<script type="text/template" id='tmp_layout'>
    <div class="row justify-content-center m-0 mt-3">
        <div class="col-12 col-xl-8 col-lg-9">
            <div class="card mb-0 shadow-sm border-0 perfil-page-card">
                <div class="card-header border-0 perfil-page-header">
                    <div>
                        <h2 class="perfil-page-title">Perfil de usuario</h2>
                        <p class="perfil-page-subtitle">
                            Consulta y actualiza los datos de acceso a la plataforma Comfaca En Línea.
                        </p>
                    </div>
                </div>
                <div class="card-body px-3 px-md-4 py-4" id='app'></div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id='tmp_perfil'>
    @include('mercurio/usuario/tmp/tmp_perfil')
</script>

<script>
    const _TITULO = "{{ $title }}";
    window.ServerController = 'usuario';
</script>

<script src="{{ asset('mercurio/build/Usuario.js') }}"></script>
@endpush
