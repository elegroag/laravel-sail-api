@extends('layouts.bone')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/summernote/summernote-bs5.min.css') }}">
<style>
    .report-page-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .report-page-header {
        padding: 1.25rem 1.5rem 0.5rem;
        border-bottom: 1px solid #eef2f7;
    }

    .report-page-title {
        margin: 0 0 0.35rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: #334155;
    }

    .report-page-subtitle {
        margin: 0;
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.55;
    }

    .report-form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(260px, 1fr);
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .report-section {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        padding: 1.15rem 1.25rem;
        margin-bottom: 1rem;
    }

    .report-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #eef2f7;
        font-size: 0.92rem;
        font-weight: 700;
        color: #334155;
    }

    .report-section-title i {
        color: #0d6efd;
    }

    .report-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .report-form-grid .form-group-full {
        grid-column: 1 / -1;
    }

    .report-form-group label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
    }

    .report-form-group .form-control,
    .report-form-group .form-select {
        border-radius: 10px;
        border-color: #dbe3ee;
        font-size: 0.9rem;
    }

    .report-form-group .form-control:focus,
    .report-form-group .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
    }

    .report-note-editor .note-editor {
        border-radius: 12px;
        overflow: hidden;
        border-color: #dbe3ee;
    }

    .report-note-editor .note-toolbar {
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
    }

    .report-note-editor .note-editable {
        min-height: 160px;
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .report-file-input {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .report-file-input input[type="file"] {
        max-width: 100%;
    }

    .report-file-hint {
        font-size: 0.78rem;
        color: #64748b;
    }

    .report-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-top: 0.25rem;
    }

    .report-aside-card {
        position: sticky;
        top: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    }

    .report-aside-card img {
        display: block;
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .report-aside-body {
        padding: 1rem 1.1rem 1.15rem;
    }

    .report-aside-body h4 {
        margin: 0 0 0.65rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #334155;
    }

    .report-aside-body p {
        margin: 0;
        font-size: 0.84rem;
        line-height: 1.6;
        color: #64748b;
    }

    .report-aside-list {
        margin: 0.85rem 0 0;
        padding-left: 1.1rem;
        font-size: 0.82rem;
        color: #64748b;
    }

    .report-aside-list li + li {
        margin-top: 0.35rem;
    }

    label.error {
        display: block;
        margin-top: 0.25rem;
        color: #dc3545;
        font-size: 0.78rem;
    }

    @media (max-width: 991.98px) {
        .report-form-layout {
            grid-template-columns: 1fr;
            padding: 1rem;
        }

        .report-form-grid {
            grid-template-columns: 1fr;
        }

        .report-aside-card {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 report-page-card">
        <div class="report-page-header">
            <h2 class="report-page-title">Reportar errores del sistema</h2>
            <p class="report-page-subtitle">
                Solicite soporte técnico al equipo de desarrollo y comparta los incidentes encontrados en la plataforma.
            </p>
        </div>
        <div id="boneLayout"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/summernote/summernote-bs5.min.js') }}"></script>
<script src="{{ asset('assets/summernote/lang/summernote-es-ES.min.js') }}"></script>

<script type="text/template" id="tmp_formulario">
    <div class="report-form-layout">
        <div class="report-form-main">
            <p class="report-page-subtitle mb-3">
                Queremos conocer la experiencia de empresas y trabajadores con el uso de la plataforma.
                Por medio de este formulario puede solicitar soporte técnico y obtener una solución rápida.
            </p>

            <form>
                <section class="report-section">
                    <h3 class="report-section-title">
                        <i class="fas fa-clipboard-list"></i>
                        Datos del reporte
                    </h3>
                    <div class="report-form-grid">
                        <div class="report-form-group form-group-full">
                            <label for="servicio">Servicio a reportar</label>
                            <select class="form-select" id="servicio" name="servicio">
                                <option value="">Seleccione un servicio</option>
                                <option value="1">Solicitud afiliación de empresas</option>
                                <option value="2">Solicitud afiliación de trabajadores</option>
                                <option value="3">Solicitud afiliación de cónyuges</option>
                                <option value="4">Solicitud afiliación de beneficiarios</option>
                                <option value="5">Solicitud actualización de datos</option>
                            </select>
                            <label id="servicio-error" class="error" for="servicio"></label>
                        </div>

                        <div class="report-form-group form-group">
                            <label for="telefono">Teléfono de contacto</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono o celular">
                            <label id="telefono-error" class="error" for="telefono"></label>
                        </div>

                        <div class="report-form-group form-group">
                            <label for="novedad">Novedad a reportar</label>
                            <select class="form-select" id="novedad" name="novedad">
                                <option value="">Seleccione una novedad</option>
                                <option value="1">Recomendación</option>
                                <option value="2">Inconsistencia en la información</option>
                                <option value="3">Error al ingresar a una funcionalidad</option>
                                <option value="4">Error en respuesta de un envío para validación</option>
                                <option value="5">Error en los datos de la empresa</option>
                                <option value="6">Error en los datos del trabajador</option>
                                <option value="7">Error en los datos del cónyuge</option>
                                <option value="8">Error en los datos del beneficiario</option>
                                <option value="9">Error en la solicitud actualización de datos</option>
                            </select>
                            <label id="novedad-error" class="error" for="novedad"></label>
                        </div>
                    </div>
                </section>

                <section class="report-section report-note-editor">
                    <h3 class="report-section-title">
                        <i class="fas fa-comment-dots"></i>
                        Detalle de la notificación
                    </h3>
                    <div class="report-form-group form-group">
                        <label for="nota">Descripción del problema</label>
                        <textarea class="form-control" id="nota" name="nota" placeholder="Describa los hechos e incidentes presentados"></textarea>
                        <label id="nota-error" class="error" for="nota"></label>
                    </div>
                </section>

                <section class="report-section">
                    <h3 class="report-section-title">
                        <i class="fas fa-paperclip"></i>
                        Evidencia
                    </h3>
                    <div class="report-form-group form-group">
                        <label for="archivo">Imagen o archivo de soporte</label>
                        <div class="report-file-input">
                            <input type="file" class="form-control" id="archivo" name="archivo" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                        </div>
                        <span class="report-file-hint">Formatos permitidos: imágenes, Word o PDF.</span>
                        <label id="archivo-error" class="error" for="archivo"></label>
                    </div>
                </section>

                <div class="report-actions">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btEnviarRegistro">
                        <i class="fas fa-paper-plane"></i>
                        <span>Enviar reporte</span>
                    </button>
                </div>
            </form>
        </div>

        <aside class="report-aside">
            <div class="report-aside-card">
                <img src="{{ asset('img/Mercurio/2022-09-05-10-34-01AM.jpeg') }}" alt="Atención al usuario Comfaca">
                <div class="report-aside-body">
                    <h4>Mejoramos con su retroalimentación</h4>
                    <p>
                        Comparta con el área de servicio técnico los problemas e inconvenientes que se le presenten
                        en Comfaca En Línea, con el ánimo de mejorar el servicio y la atención a nuestros afiliados.
                    </p>
                    <ul class="report-aside-list">
                        <li>Describa el paso a paso del incidente.</li>
                        <li>Adjunte capturas cuando sea posible.</li>
                        <li>Indique un teléfono de contacto válido.</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</script>

<script>
    const _TITULO = "{{ $title }}";
    window.ServerController = 'notificaciones';
</script>

<script src="{{ versioned_asset('mercurio/build/Notificaciones.js') }}"></script>
@endpush
