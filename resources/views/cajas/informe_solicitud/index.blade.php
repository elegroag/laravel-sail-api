@extends('layouts.cajas')

@push('styles')
    <style>
        .informe-page-card {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08);
        }

        .informe-page-card .card-header {
            border: 0;
            background: linear-gradient(120deg, #0d6efd 0%, #0a58ca 55%, #084298 100%);
            color: #ffffff;
            padding: 1.1rem 1.35rem;
        }

        .informe-page-card .card-header h2,
        .informe-page-card .card-header p {
            color: #ffffff !important;
            margin: 0;
        }

        .informe-page-card .card-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .informe-page-card .card-header p {
            margin-top: 0.35rem;
            opacity: 0.95;
            font-size: 0.875rem;
        }

        .informe-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.1rem 0.35rem;
            margin-bottom: 1rem;
        }

        .informe-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .informe-filters .form-control-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }

        .informe-actions {
            display: flex;
            justify-content: center;
            margin-top: 0.75rem;
        }

        .informe-actions .btn {
            min-width: 200px;
            min-height: 44px;
            font-weight: 600;
        }

        .informe-info {
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

        .informe-info i {
            color: #0d6efd;
            margin-top: 0.15rem;
        }
    </style>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <div class="card informe-page-card">
                <div class="card-header text-white">
                    <h2 class="text-white mb-0">{{ $title ?? 'Informe de solicitud' }}</h2>
                    <p class="text-white mb-0">Genere el PDF con los datos de la solicitud y su trazabilidad de eventos.</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="informe-info" role="note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <div>
                            Ingrese el <strong>RUUID</strong> de la solicitud y el <strong>tipo de afiliación</strong>.
                            El informe incluye los datos registrados y el historial desde el envío hasta la aprobación.
                        </div>
                    </div>

                    <form
                        id="form-informe-solicitud"
                        method="GET"
                        action="{{ route('cajas.informe-solicitud.pdf') }}"
                        target="_blank"
                        autocomplete="off"
                        novalidate>
                        <div class="informe-filters">
                            <div class="informe-section-title">Parámetros del informe</div>
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="ruuid" class="form-control-label">
                                            RUUID <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="ruuid"
                                            name="ruuid"
                                            class="form-control"
                                            maxlength="40"
                                            required
                                            aria-required="true"
                                            placeholder="Ej. radicado de la solicitud"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="tipopc" class="form-control-label">
                                            Tipo de afiliación <span class="text-danger">*</span>
                                        </label>
                                        <select id="tipopc" name="tipopc" class="form-control" required aria-required="true">
                                            <option value="">Seleccione</option>
                                            @foreach ($mercurio09 as $tipo)
                                                <option value="{{ $tipo->tipopc }}">{{ $tipo->detalle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="informe-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-pdf mr-1" aria-hidden="true"></i>
                                Descargar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
