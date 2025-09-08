<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
                <div class="d-none">
                    <input type="number" name="id" class="d-none" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                    <input type="text" name="codsuc" class="d-none">
                    <input type="number" name="tipo_actualizacion" class="d-none">
                </div>
                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Actualización datos empleador</legend>
                            <div class="row">
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
                                        <input type="text" name="razsoc" class="form-control" placeholder="Razon Social" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <div class="form-group form-item">
                                        <label for="sigla" class="control-label">Sigla:</label>
                                        <input type="text" name="sigla" class="form-control" placeholder="Sigla" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <div class="form-group form-item">
                                        <label for="matmer" class="control-label">Matrícula mercantil:</label>
                                        <input type="text" name="matmer" class="form-control" placeholder="Matricula Mercantil" oninput="this.value = this.value.toUpperCase()">
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
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="codact" class="control-label top">Actividad Economica CIUU-DIAN:</label>
                                        <span id='component_codact'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="dirpri" class="control-label top ml-4">Dirección comercial:</label>
                                        <input type="text" name="dirpri" class="form-control" placeholder="Dirección comercial">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="barrio_comercial" class="control-label">Barrio comercial:</label>
                                        <input type="text" name="barrio_comercial" class="form-control" placeholder="barrio dirección comercial" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="ciupri" class="control-label top">Ciudad comercial:</label>
                                        <span id='component_ciupri'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="telpri" class="control-label">Teléfono comercial:</label>
                                        <input type="number" name="telpri" class="form-control" placeholder="Telefono Principal con Indicativo" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="celpri" class="control-label">Celular comercial:</label>
                                        <input type="number" name="celpri" class="form-control" placeholder="Celular Principal" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="emailpri" class="control-label">Email comercial:</label>
                                        <input type="text" name="emailpri" class="form-control" placeholder="Email Principal" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos representante</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="coddocrepleg" class="control-label top">Tipo documento representante</label>
                                        <span id='component_coddocrepleg'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="cedrep" class="control-label">Identificación representante</label>
                                        <input type="text" name="cedrep" class="form-control" placeholder="Cedula representante" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="priape" class="control-label">Primer apellido representante:</label>
                                        <input type="text" name="priape" class="form-control" placeholder="Primer Apellido" oninput="this.value = this.value.toUpperCase()">
                                        <label id="priape-error" class="error" for="priape"></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="segape" class="control-label">Segundo apellido representante:</label>
                                        <input type="text" name="segape" class="form-control" placeholder="Segundo Apellido" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="prinom" class="control-label">Primer nombre representante:</label>
                                        <input type="text" name="prinom" class="form-control" placeholder="Primer Nombre" oninput="this.value = this.value.toUpperCase()">
                                        <label id="prinom-error" class="error" for="prinom"></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="segnom" class="control-label">Segundo nombre representante:</label>
                                        <input type="text" name="segnom" class="form-control" placeholder="Segundo Nombre" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos de contacto</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="direccion" class="control-label top ml-4">Dirección notificación:</label>
                                        <input type="text" name="direccion" class="form-control" placeholder="Direccion">
                                        <label id="direccion-error" class="error" for="direccion"></label>
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
                                        <input type="number" name="telefono" class="form-control" placeholder="Telefono con Indicativo" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="barrio_notificacion" class="control-label">Barrio notificaciónes:</label>
                                        <input type="text" name="barrio_notificacion" class="form-control" placeholder="Telefono con Indicativo" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="celular" class="control-label">Celular notificación</label>
                                        <input type="number" name="celular" class="form-control" placeholder="Celular" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="email" class="control-label">Email notificación empresarial</label>
                                        <input type="text" name="email" class="form-control" placeholder="Email" oninput="this.value = this.value.toUpperCase()">
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
