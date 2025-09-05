@extends('layouts.bone')

@push('styles')
<script src="{{ asset('assets/summernote/summernote-bs5/css/summernote-bs5.min.css') }}"></script>
@endpush

@section('content')
<div class='card-header' id='afiliacion_header'>
    <h3>REPORTAR</h3>
</div>

<div class="col-auto m-2">
    <div id='boneLayout'></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/summernote/summernote-bs5/js/summernote-bs5.min.js') }}"></script>
<script src="{{ asset('assets/summernote/lang/summernote-es-ES.min.js') }}"></script>

<script type='text/template' id='tmp_formulario'>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p>Queremos conocer la experiencia de las empresas y los trabajadores con el uso de la plataforma y todas las actualizaciones que se han presentado.
                    Por medio de este formulario, puedes solicitar soporte técnico, al mismo equipo de desarrollo de la plataforma, y poder obtener una solución rapida.</p>
                <form>
                    <div class="form-group">
                        <label for="servicio">Servicio a reportar:</label>
                        <select type="text" class="form-control form-control-sm" id="servicio" name="servicio" placeholder="seleccionar aquí...">
                            <option value="">Ninguno</option>
                            <option value="1">Solicitud afiliación de empresas</option>
                            <option value="2">Solicitud afiliación de trabajadores</option>
                            <option value="3">Solicitud afiliación de conyuges</option>
                            <option value="4">Solicitud afiliación de beneficiarios</option>
                            <option value="5">Solicitud actualización de datos</option>
                        </select>
                        <label id="servicio-error" class="error" for="servicio"></label>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="telefono">Teléfono de contacto:</label>
                            <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" placeholder="teléfono">
                            <label id="telefono-error" class="error" for="telefono"></label>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="novedad">Novedad a reportar:</label>
                            <select type="text" class="form-control form-control-sm" id="novedad" name="novedad" placeholder="novedad">
                                <option value="">Ninguno</option>
                                <option value="1">Recomendación</option>
                                <option value="2">Inconsistencia en la información</option>
                                <option value="3">Error en al ingresar a una funcionalidad</option>
                                <option value="4">Error en respuesta de un envío para validación</option>
                                <option value="5">Error en los datos de la empresa</option>
                                <option value="6">Error en los datos del trabajador</option>
                                <option value="7">Error en los datos del conyuge</option>
                                <option value="8">Error en los datos del beneficiario</option>
                                <option value="9">Error en la solicitud actualización de datos</option>
                            </select>
                            <label id="novedad-error" class="error" for="novedad"></label>
                        </div>
                    </div>
                </form>
                <div class="form-group">
                    <label for="nota">Notificación:</label>
                    <textarea type="text" class="form-control" id="nota" name="nota" placeholder="Detalles del problema"></textarea>
                    <label id="nota-error" class="error" for="nota"></label>
                </div>

                <div class="form-group">
                    <label for="archivo">Imagen de evidencia del problema:</label>
                    <input type="file" class="form-control" id="archivo" name="archivo"/>
                    <label id="archivo-error" class="error" for="archivo"></label>
                </div>
                <br />
                <button type="submit" class="btn btn-primary" id='btEnviarRegistro'>Envíar</button>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <?php echo Tag::image("Mercurio/2022-09-05-10-34-01AM.jpeg", "class: navbar-brand-img"); ?>
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <p class="card-text text-left">Con el fin de mejorar la experiencia de los usuarios en la plataforma "Comfaca En Línea",
                            comparte con el área de servicio técnico los problemas e incovenientes que se les presentan.
                            Todo esto con el animo de mejorar el servicio y la atención a nuestros afiliados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<script src="{{ asset('mercurio/Notificaciones.js') }}"></script>
@endpush

@section('styles')
<style>
    label.error {
        color: red;
        font-size: 0.81rem;
    }

    .card-text,
    label {
        font-size: 0.85rem;
    }

    .navbar-brand-img,
    .navbar-brand>img {
        max-width: initial;
    }
</style>
@endsection
