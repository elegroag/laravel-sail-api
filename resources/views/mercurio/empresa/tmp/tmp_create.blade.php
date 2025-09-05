<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate class="mb-3">
                <div class="d-none">
                    <input type="number" id="id" name="id" class="d-none" />
                    <input type="text" id="tipdoc" name="tipdoc" class="d-none" />
                    <input type="text" id="calemp" name="calemp" class="d-none" value="E" />
                </div>
                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Generales empleador</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tipper'>
                                        <label for="tipper" class="control-label">Tipo persona:</label>
                                        <span id='component_tipper'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='nit'>
                                        <label for="nit" class="control-label">NIT o documento empresa:</label>
                                        <input type="number" id="nit" name="nit" class="form-control" placeholder="nit" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='razsoc'>
                                        <label for="razsoc" class="control-label">Razón social:</label>
                                        <input type="text" id="razsoc" name="razsoc" class="form-control uppercase" placeholder="Razon social" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='coddoc'>
                                        <label for="coddoc" class="control-label">Tipo documento empresa:</label>
                                        <span id='component_coddoc'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='digver'>
                                        <label for="digver" class="control-label">Digito verificación:</label>
                                        <input type="number" id="digver" name="digver" class="form-control" placeholder="digver" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='sigla'>
                                        <label for="sigla" class="control-label">Sigla:</label>
                                        <input type="text" id="sigla" name="sigla" class="form-control uppercase" placeholder="Sigla" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='matmer'>
                                        <label for="matmer" class="control-label">Matricula mercantil:</label>
                                        <input type="text" id="matmer" name="matmer" class="form-control uppercase" placeholder="Matricula Mercantil" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='dirpri'>
                                        <label for="dirpri" class="control-label">Dirección comercial:</label>
                                        <input type="text" id="dirpri" name="dirpri" class="form-control" placeholder="dirección comercial" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tipsoc'>
                                        <label for="tipsoc" class="control-label">Tipo sociedad:</label>
                                        <span id='component_tipsoc'></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <br />

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Especifico del empleador</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tipemp'>
                                        <label for="tipemp" class="control-label">Tipo empresa:</label>
                                        <span id='component_tipemp'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='codzon'>
                                        <label for="codzon" class="control-label">Ciudad laboran trabajadores:</label>
                                        <span id='component_codzon'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='codact'>
                                        <label for="codact" class="control-label">CIUU-DIAN Actividad economica:</label>
                                        <span id='component_codact'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='fecini'>
                                        <label for="fecini" class="control-label">Fecha inicio:</label>
                                        <input type="date" id="fecini" name="fecini" class="form-control" placeholder="Fecha inicio" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tottra'>
                                        <label for="tottra" class="control-label">Total trabajadores:</label>
                                        <input type="number" id="tottra" name="tottra" class="form-control" placeholder="Total trabajadores" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='valnom'>
                                        <label for="valnom" class="control-label">Valor nomina:</label>
                                        <input type="number" id="valnom" name="valnom" class="form-control" placeholder="Valor nomina" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='codcaj'>
                                        <label for="codcaj" class="control-label">Caja a la que estuvo afiliado antes:</label>
                                        <span id='component_codcaj'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='ciupri'>
                                        <label for="ciupri" class="control-label">Ciudad comercial:</label>
                                        <span id='component_ciupri'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='telpri'>
                                        <label for="telpri" class="control-label">Telefono comercial con indicativo:</label>
                                        <input type="number" id="telpri" name="telpri" class="form-control" placeholder="Telefono comercial con indicativo" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='celpri'>
                                        <label for="celpri" class="control-label">Celular comercial:</label>
                                        <input type="number" id="celpri" name="celpri" class="form-control" placeholder="Celular comercial" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='emailpri'>
                                        <label for="emailpri" class="control-label">Email comercial:</label>
                                        <input type="text" id="emailpri" name="emailpri" class="form-control uppercase" placeholder="Email comercial" />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <br />

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Representante legal</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" group-for='coddocrepleg'>
                                        <label for="coddocrepleg" class="control-label">Tipo documento representante:</label>
                                        <span id='component_coddocrepleg'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='cedrep'>
                                        <label for="cedrep" class="control-label">Identificación representante:</label>
                                        <input type="number" id="cedrep" name="cedrep" class="form-control" placeholder="Cedula representante" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='priape'>
                                        <label for="priape" class="control-label">Primer apellido:</label>
                                        <input type="text" id="priape" name="priape" class="form-control uppercase" placeholder="Primer apellido" />
                                        <label id="priape-error" class="error" for="priape"></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='segape'>
                                        <label for="segape" class="control-label">Segundo apellido:</label>
                                        <input type="text" id="segape" name="segape" class="form-control uppercase" placeholder="Segundo apellido" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='prinom'>
                                        <label for="prinom" class="control-label">Primer nombre:</label>
                                        <input type="text" id="prinom" name="prinom" class="form-control uppercase" placeholder="Primer nombre" />
                                        <label id="prinom-error" class="error" for="prinom"></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='segnom'>
                                        <label for="segnom" class="control-label">Segundo nombre:</label>
                                        <input type="text" id="segnom" name="segnom" class="form-control uppercase" placeholder="Segundo nombre" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='repleg'>
                                        <label for="repleg" class="control-label">Representante legal:</label>
                                        <input type="text" id="repleg" name="repleg" class="form-control uppercase" placeholder="Representante legal" readonly />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <br />

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Contacto administrativo</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" group-for='codciu'>
                                        <label for="codciu" class="control-label">Ciudad notificación:</label>
                                        <span id='component_codciu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='direccion'>
                                        <label for="direccion" class="control-label">Dirección notificación:</label>
                                        <input type="text" id="direccion" name="direccion" class="form-control" placeholder="dirección notificación" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='telefono'>
                                        <label for="telefono" class="control-label">Telefono notificación con indicativo:</label>
                                        <input type="number" id="telefono" name="telefono" class="form-control" placeholder="Telefono con indicativo" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='email'>
                                        <label for="email" class="control-label">Email notificación</label>
                                        <input type="text" id="email" name="email" class="form-control uppercase" placeholder="Email notificación" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='fax'>
                                        <label for="fax" class="control-label">Fax notificación</label>
                                        <input type="text" id="fax" name="fax" class="form-control uppercase" placeholder="Fax" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='celular'>
                                        <label for="celular" class="control-label">Celular notificación</label>
                                        <input type="number" id="celular" name="celular" class="form-control" placeholder="Celular notificación" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='autoriza'>
                                        <label for="autoriza" class="control-label">Autoriza el tratamiento de datos personales</label>
                                        <span id='component_autoriza'></span>
                                    </div>
                                </div>
                        </fieldset>
                    </div>
                </div>
                <br />

                <div class="row">
                    <% if($estado == 'T' || $estado == 'D' || $estado == null){ %>
                    <div class="col-6">
                        <fieldset>
                            <legend>Relaciona trabajadores en nomina</legend>
                            <p>Certifica que las personas relacionadas en este formulario está en la nomina, son empleados de la empresa y sus labores son realizadas en el Departamento del Caquetá</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" group-for='cedtra'>
                                        <label for="cedtra" class="control-label">Identificación trabajador:</label>
                                        <input type="number" id="cedtra" name="cedtra" class="form-control" placeholder="Cédula de identidad" />
                                        <label id="cedtra-error" class="error" toggle-error="cedtra"></label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" group-for='nomtra'>
                                        <label for="nomtra" class="control-label">Nombres trabajador:</label>
                                        <input type="text" id="nomtra" name="nomtra" class="form-control uppercase" placeholder="Nombres trabajador" />
                                        <label id="nomtra-error" class="error" toggle-error="nomtra"></label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" group-for='apetra'>
                                        <label for="apetra" class="control-label">Apellidos trabajador:</label>
                                        <input type="text" id="apetra" name="apetra" class="form-control uppercase" placeholder="Apellidos trabajador" />
                                        <label id="apetra-error" class="error" toggle-error="apetra"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" group-for='saltra'>
                                        <label for="saltra" class="control-label">Salario:</label>
                                        <input type="number" id='saltra' name='saltra' class='form-control number' placeholder="Salario" />
                                        <label id="saltra-error" class="error" toggle-error="saltra"></label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" group-for='fectra'>
                                        <label for="fectra" class="control-label">Fecha inicia labores:</label>
                                        <input type="date" id="fectra" name="fectra" class="form-control" placeholder="Fecha de inicio labores" />
                                        <label id="fectra-error" class="error" toggle-error="fectra"></label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" group-for='cartra'>
                                        <label for="cartra" class="control-label">Cargo:</label>
                                        <input type="text" id="cartra" name="cartra" class="form-control uppercase" placeholder="Cargo" />
                                        <label id="cartra-error" class="error" toggle-error="cartra"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mb-3">
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary btn-sm btn-block" id='add_trabajador'>
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-default btn-sm btn-block" id='clean_formtra'>
                                        <i class="fas fa-time"></i> Cancelar
                                    </button>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                    <% } %>
                    <div class="col-6">
                        <fieldset>
                            <legend>Trabajadores en nomina</legend>
                            <table class="table align-items-center mb-0 table-bordered mb-3" id='tableTranomRow'>
                                <thead>
                                    <tr>
                                        <th class='text-uppercase text-primary'>ID</th>
                                        <th class='text-uppercase text-primary'>NOMBRE</th>
                                        <th class='text-uppercase text-primary'>FECHA INICIA</th>
                                        <th class='text-uppercase text-primary' width='10%'>OPT</th>
                                    </tr>
                                </thead>
                            </table>
                        </fieldset>
                    </div>
                </div>
            </form>

            <% if($estado == 'T' || $estado == 'D' || $estado == null){  %>
            <div class="row justify-content-center">
                <div class="col-3">
                    <button type="button" class="btn btn-primary btn-block" id='guardar_ficha'>
                        <i class="fas fa-save"></i> Guardar y continuar
                    </button>
                </div>
            </div>
            <% } %>
        </div>
    </div>
    <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">...</div>
    <div class="tab-pane fade" id="documentos_adjuntos" role="tabpanel" aria-labelledby="documentos_adjuntos-tab">...</div>
    <div class="tab-pane fade" id="firma" role="tabpanel" aria-labelledby="firma-tab">...</div>
    <div class="tab-pane fade" id="enviar_radicado" role="tabpanel" aria-labelledby="enviar_radicado-tab">
        @include('templates/tmp_send_radicado')
    </div>
</div>
