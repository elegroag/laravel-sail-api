@extends('layouts.bone')

@section('content')
@php
    $beneficiariosUi = [];
    if ($subsi22) {
        foreach ($subsi22 as $beneficiarioCerti) {
            $certDisponibles = [];
            if (! empty($beneficiarioCerti['certificadoPendiente']) && ! empty($beneficiarioCerti['certificados'])) {
                foreach ($beneficiarioCerti['codcer'] as $ai => $value) {
                    $has = false;
                    foreach ($beneficiarioCerti['certificados'] as $certificado) {
                        if ((string) $ai === (string) $certificado->getCodcer()) {
                            $has = true;
                            break;
                        }
                    }
                    if (! $has) {
                        $certDisponibles[$ai] = $value;
                    }
                }
            } else {
                $certDisponibles = $beneficiarioCerti['codcer'] ?? [];
            }

            $beneficiariosUi[] = [
                'codben' => (string) $beneficiarioCerti['codben'],
                'nombre' => capitalize($beneficiarioCerti['nombre']),
                'ultfec' => $beneficiarioCerti['ultfec'] ?? '',
                'certificados' => $certDisponibles,
                'pendiente' => count($certDisponibles) === 0,
            ];
        }
    }
    $puedeCargar = collect($beneficiariosUi)->contains(fn ($b) => ! $b['pendiente']);
@endphp

<div class="header bg-gradient-primary pb-9">
    <div class="container-fluid">
        <div class="header-body p-4">
            <div id="header_group_button">
                <div class="row justify-content-start">
                    <div class="col-xs-12 col-auto">
                        <h4 class="text-white d-inline-block mb-0">Presentar Certificados</h4>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><span class="text-white"><i class="fas fa-file-pdf"></i></span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col">
            <div class="card certificados-page">
                <div class="card-header bg-green-blue p-1" id="render_subeader"></div>
                <div class="card-body certificados-page__body">
                    <div class="certificados-layout">
                        <section class="certificados-main">
                            <header class="certificados-main__header">
                                <h3 class="certificados-main__title">Cargar certificado</h3>
                                <p class="certificados-main__subtitle mb-0">
                                    Selecciona el beneficiario, el tipo de certificado y adjunta el PDF.
                                </p>
                            </header>

                            @if (! $subsi22)
                                <div class="certificados-empty">
                                    <i class="fas fa-info-circle"></i>
                                    <p class="mb-0">No dispone de beneficiarios pendientes por cargue de certificados.</p>
                                </div>
                            @elseif (! $puedeCargar)
                                <div class="certificados-empty">
                                    <i class="fas fa-clock"></i>
                                    <p class="mb-0">Los certificados de sus beneficiarios están pendientes de validación.</p>
                                </div>
                            @else
                                <form id="formCertificado" class="certificados-form" autocomplete="off" novalidate>
                                    <div class="certificados-form__row">
                                        <div class="form-group mb-0" group-for="codben">
                                            <label for="codben" class="control-label">Beneficiario</label>
                                            <select name="codben" id="codben" class="form-control" required>
                                                <option value="">Seleccione beneficiario</option>
                                                @foreach ($beneficiariosUi as $ben)
                                                    @if (! $ben['pendiente'])
                                                        <option value="{{ $ben['codben'] }}">{{ $ben['nombre'] }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-0" group-for="codcer">
                                            <label for="codcer" class="control-label">Tipo de certificado</label>
                                            <select name="codcer" id="codcer" class="form-control" required disabled>
                                                <option value="">Seleccione primero un beneficiario</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="certificados-dropzone doc-dropzone" id="certDropzone" data-busy="0">
                                        <input
                                            type="file"
                                            class="doc-dropzone__input"
                                            id="archivo"
                                            name="archivo"
                                            accept="application/pdf,.pdf"
                                        />
                                        <div class="doc-dropzone__content" id="certDropzoneContent">
                                            <div class="doc-dropzone__icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                            <div class="doc-dropzone__title">Arrastra el PDF aquí o haz clic para seleccionar</div>
                                            <div class="doc-dropzone__hint">Solo PDF. Máximo un archivo.</div>
                                        </div>
                                        <div class="doc-dropzone__loading d-none" id="certDropzoneLoading">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Subiendo certificado…
                                        </div>
                                    </div>

                                    <div class="certificados-file-meta d-none" id="certFileMeta">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        <span id="certFileName"></span>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" id="certFileClear" title="Quitar archivo">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="certificados-actions">
                                        <button type="button" class="btn btn-primary certificados-actions__btn" id="btnSalvarCertificado">
                                            <i class="fas fa-paper-plane me-2"></i>Enviar certificado
                                        </button>
                                        <p class="certificados-actions__hint mb-0">El documento queda en estado pendiente hasta la validación de la Caja.</p>
                                    </div>
                                </form>
                            @endif
                        </section>

                        <aside class="certificados-aside">
                            <h3 class="certificados-aside__title">Beneficiarios</h3>
                            @if (! $subsi22)
                                <p class="certificados-aside__text">Sin información de núcleo para presentar certificados.</p>
                            @else
                                <ul class="certificados-ben-list list-unstyled mb-0">
                                    @foreach ($beneficiariosUi as $ben)
                                        <li class="certificados-ben-item {{ $ben['pendiente'] ? 'certificados-ben-item--pending' : '' }}">
                                            <div class="certificados-ben-item__name">{{ $ben['nombre'] }}</div>
                                            <div class="certificados-ben-item__meta">
                                                @if ($ben['pendiente'])
                                                    Pendiente de validación
                                                @else
                                                    {{ $ben['ultfec'] ?: 'Disponible para cargue' }}
                                                @endif
                                            </div>
                                            <span class="certificados-ben-item__badge">
                                                {{ $ben['pendiente'] ? 'En revisión' : count($ben['certificados']).' tipo(s)' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <p class="certificados-aside__alert mb-0">Solo se admiten archivos PDF.</p>
                        </aside>
                    </div>

                    @if ($certificadosPresentados && count($certificadosPresentados) > 0)
                        <section class="certificados-presentados">
                            <header class="certificados-presentados__header">
                                <h3 class="certificados-presentados__title">Certificados presentados</h3>
                                <p class="certificados-presentados__subtitle mb-0">Solicitudes enviadas a la Caja.</p>
                            </header>
                            <div class="table-responsive">
                                <table class="table table-sm certificados-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:10%">Código</th>
                                            <th>Beneficiario</th>
                                            <th>Certificado</th>
                                            <th style="width:14%">Fecha</th>
                                            <th style="width:14%">Estado</th>
                                            <th style="width:12%" class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($certificadosPresentados as $certPresentado)
                                            <tr data-id="{{ $certPresentado->getId() }}">
                                                <td>{{ $certPresentado->getCodben() }}</td>
                                                <td>{{ capitalize($certPresentado->getNombre()) }}</td>
                                                <td>{{ capitalize($certPresentado->getNomcer()) }}</td>
                                                <td>{{ $certPresentado->getFecha()?->format('Y-m-d') ?? $certPresentado->getFecha() }}</td>
                                                <td>
                                                    <span class="certificados-status certificados-status--{{ strtolower($certPresentado->getEstado()) }}">
                                                        {{ capitalize($certPresentado->getEstadoDetalle()) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if (in_array($certPresentado->getEstado(), ['P', 'D', 'T'], true))
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm certificados-btn-archivar"
                                                            data-id="{{ $certPresentado->getId() }}"
                                                            data-nombre="{{ capitalize($certPresentado->getNombre()) }}"
                                                            data-nomcer="{{ capitalize($certPresentado->getNomcer()) }}"
                                                            title="Eliminar solicitud">
                                                            <i class="fas fa-trash-alt"></i>
                                                            <span class="d-none d-md-inline">Eliminar</span>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const _TITULO = @json($title);
        window.ServerController = 'certificados';
        window.CertificadosBeneficiarios = @json($beneficiariosUi);
    </script>
    <script src="{{ asset('core/upload.js') }}"></script>
    <script src="{{ versioned_asset('mercurio/build/Certificados.js') }}"></script>
@endpush
