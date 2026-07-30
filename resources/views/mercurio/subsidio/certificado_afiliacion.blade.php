@extends('layouts.bone')

@push('styles')
    <style>
        .certificado-page-card {
            border-radius: 16px;
            overflow: hidden;
        }

        .certificado-page-header {
            padding: 1.25rem 1.5rem 0.5rem;
            border-bottom: 1px solid #eef2f7;
        }

        .certificado-page-title {
            margin: 0 0 0.35rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: #334155;
        }

        .certificado-page-subtitle {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.55;
        }

        .certificado-form-section {
            padding: 1.25rem 1.1rem;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
        }

        .certificado-form-section .form-group label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .certificado-form-section .form-control {
            border-radius: 10px;
        }

        .certificado-form-actions {
            display: flex;
            align-items: flex-end;
            height: 100%;
        }

        .certificado-form-actions .btn {
            min-width: 12rem;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.65rem 1.25rem;
        }

        @media (max-width: 767.98px) {
            .certificado-form-actions {
                margin-top: 0.5rem;
            }

            .certificado-form-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
<script src="{{ versioned_asset('mercurio/build/GeneradorCertificado.js') }}"></script>
@endpush

@section('title', 'Certificados de Trabajador')

@section('content')
<div class="col-12 col-xl-8 col-lg-9 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 certificado-page-card">
        <div class="card-header border-0 certificado-page-header">
            <div>
                <h2 class="certificado-page-title">{{ $title ?? 'Certificado de trabajador' }}</h2>
                <p class="certificado-page-subtitle">
                    Selecciona el tipo de certificado y genera el documento en PDF.
                </p>
            </div>
        </div>

        <div class="card-body px-3 px-md-4 py-4">
            <div class="certificado-form-section">
                <form id="form"
                      class="validation_form"
                      autocomplete="off"
                      novalidate
                      action="{{ url('mercurio/subsidio/certificado_afiliacion') }}"
                      method="POST"
                      target="_blank">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            @component('components.select-field',
                            [
                                'name' => 'tipo',
                                'id' => 'tipo',
                                'class' => 'form-control',
                                'options' => $tipo,
                                'dummy' => 'Seleccione',
                                'label' => 'Tipo certificado'
                            ])
                            @endcomponent
                        </div>
                        <div class="col-md-4">
                            <div class="certificado-form-actions">
                                <button type="button" class="btn btn-primary w-100" id="bt_certificado_afiliacion">
                                    <i class="fas fa-file-certificate me-1" aria-hidden="true"></i>
                                    Generar certificado
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
