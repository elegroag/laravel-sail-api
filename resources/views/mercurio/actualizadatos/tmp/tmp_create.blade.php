<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
                <div class="d-none">
                    <input type="text" name="id" id="id" class="d-none" />
                    <input type="text" name="tipact" id="tipact" class="d-none" />
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Actualización datos empleador</legend>
                            <div class="row justify-content-around">
                                <div class="col-md-4">
                                    <div class="form-group form-item">
                                        <label for="codsuc" class="control-label top">Sucursal:</label>
                                        <span id='component_codsuc'></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <div class="form-group form-item">
                                        <label for="tipper" class="control-label top">Tipo persona comercial:</label>
                                        <span id='component_tipper'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="tipdoc" class="control-label top">Tipo documento empresa:</label>
                                        <span id='component_tipdoc'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="razsoc" class="control-label">Razón social:</label>
                                        <span id='component_razsoc'></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <div class="form-group form-item">
                                        <label for="sigla" class="control-label">Sigla:</label>
                                        <span id='component_sigla'></span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <div class="form-group form-item">
                                        <label for="matmer" class="control-label">Matrícula mercantil:</label>
                                        <span id='component_matmer'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="tipsoc" class="control-label top">Tipo sociedad:</label>
                                        <span id='component_tipsoc'></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-item">
                                        <label for="codzon" class="control-label top">Lugar donde laboran trabajadores:</label>
                                        <span id='component_codzon'></span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group form-item">
                                        <label for="codact" class="control-label top">Actividad Economica CIUU-DIAN:</label>
                                        <span id='component_codact'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="dirpri" class="control-label">Dirección comercial:</label>
                                        <span id='component_dirpri'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="barrio_comercial" class="control-label">Barrio comercial:</label>
                                        <span id='component_barrio_comercial'></span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group form-item">
                                        <label for="ciupri" class="control-label top">Ciudad comercial:</label>
                                        <span id='component_ciupri'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="telpri" class="control-label">Teléfono comercial:</label>
                                        <span id='component_telpri'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="celpri" class="control-label">Celular comercial:</label>
                                        <span id='component_celpri'></span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group form-item">
                                        <label for="emailpri" class="control-label">Email comercial:</label>
                                        <span id='component_emailpri'></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos representante</legend>
                            <div class="row justify-content-around">
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="coddocrepleg" class="control-label top">Tipo documento representante</label>
                                        <span id='component_coddocrepleg'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="cedrep" class="control-label">Identificación representante</label>
                                        <span id='component_cedrep'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="priape" class="control-label">Primer apellido representante:</label>
                                        <span id='component_priape'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="segape" class="control-label">Segundo apellido representante:</label>
                                        <span id='component_segape'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="prinom" class="control-label">Primer nombre representante:</label>
                                        <span id='component_prinom'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="segnom" class="control-label">Segundo nombre representante:</label>
                                        <span id='component_segnom'></span>
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos de contacto</legend>
                            <div class="row justify-content-around">
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="direccion" class="control-label">Dirección de notificación:</label>
                                        <span id='component_direccion'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="codciu" class="control-label top">Ciudad notificación:</label>
                                        <span id='component_codciu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="telefono" class="control-label">Teléfono notificación con indicativo:</label>
                                        <span id='component_telefono'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="barrio_notificacion" class="control-label">Barrio notificaciónes:</label>
                                        <span id='component_barrio_notificacion'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="celular" class="control-label">Celular notificación</label>
                                        <span id='component_celular'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="email" class="control-label">Email notificación empresarial</label>
                                        <span id='component_email'></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>

        <% if (estado == 'T' || estado == 'D' || estado == void 0) { %>
            <div class="card-footer">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" id='guardar_ficha'>
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        <% } %>
    </div>
    <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">...</div>
    <div class="tab-pane fade" id="documentos_adjuntos" role="tabpanel" aria-labelledby="documentos_adjuntos-tab">...</div>
    <div class="tab-pane fade" id="firma" role="tabpanel" aria-labelledby="firma-tab">...</div>
    <div class="tab-pane fade" id="enviar_radicado" role="tabpanel" aria-labelledby="enviar_radicado-tab">
        @include('mercurio.templates.tmp_send_radicado')
    </div>
</div>
