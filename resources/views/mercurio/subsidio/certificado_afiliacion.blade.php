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

        .certificado-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.1rem;
        }

        .certificado-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.65rem;
            height: 100%;
            padding: 1.35rem 1rem 1.1rem;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .certificado-item:hover,
        .certificado-item:focus-visible {
            transform: translateY(-4px);
            border-color: rgba(13, 110, 253, 0.35);
            box-shadow: 0 10px 24px rgba(13, 110, 253, 0.14);
            outline: none;
        }

        .certificado-item:active {
            transform: translateY(-1px);
        }

        .certificado-item.is-disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .certificado-item__figure {
            width: 104px;
            height: 104px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #eef2f7;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .certificado-item__image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .certificado-item__title {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 700;
            color: #334155;
            line-height: 1.35;
        }

        .certificado-item__description {
            margin: 0;
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.5;
            flex-grow: 1;
        }

        .certificado-item__action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            padding: 0.55rem 0.9rem;
            border-radius: 10px;
            background: #0d6efd;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 600;
            transition: background-color 0.18s ease;
        }

        .certificado-item:hover .certificado-item__action {
            background: #0b5ed7;
        }

        .certificado-item__loading {
            display: none;
        }

        .certificado-item.is-loading .certificado-item__action {
            background: #94a3b8;
        }

        .certificado-item.is-loading .certificado-item__action-text {
            display: none;
        }

        .certificado-item.is-loading .certificado-item__loading {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        @media (max-width: 575.98px) {
            .certificado-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
<script src="{{ versioned_asset('mercurio/build/GeneradorCertificado.js') }}"></script>
@endpush

@section('title', 'Certificados de Trabajador')

@section('content')
<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 certificado-page-card">
        <div class="card-header border-0 certificado-page-header">
            <div>
                <h2 class="certificado-page-title">{{ $title ?? 'Certificados de trabajador' }}</h2>
                <p class="certificado-page-subtitle">
                    Pulse el recuadro del certificado que desea procesar y descargar.
                </p>
            </div>
        </div>

        <div class="card-body px-3 px-md-4 py-4">
            <form id="form"
                  class="validation_form"
                  autocomplete="off"
                  novalidate
                  action="{{ url('mercurio/subsidio/certificado_afiliacion') }}"
                  method="POST"
                  target="_blank">
                @csrf
                <input type="hidden" name="tipo" id="tipo" value="" />
                <div class="certificado-grid">
                    @foreach ($certificados as $certificado)
                        <div class="certificado-item"
                             role="button"
                             tabindex="0"
                             data-certificado-tipo="{{ $certificado['codigo'] }}"
                             data-certificado-titulo="{{ $certificado['titulo'] }}">
                            <figure class="certificado-item__figure">
                                <img src="{{ versioned_asset($certificado['imagen']) }}"
                                     alt="{{ $certificado['titulo'] }}"
                                     class="certificado-item__image" />
                            </figure>
                            <h3 class="certificado-item__title">{{ $certificado['titulo'] }}</h3>
                            <p class="certificado-item__description">{{ $certificado['descripcion'] }}</p>
                            <span class="certificado-item__action">
                                <i class="fas {{ $certificado['icono'] }}" aria-hidden="true"></i>
                                <span class="certificado-item__action-text">Procesar certificado</span>
                                <span class="certificado-item__loading">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Procesando
                                </span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
