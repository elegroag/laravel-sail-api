
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
            <div class="d-none">
                <input type="number" name="id" class="d-none"/>
                <input type="text" name="calemp" class="d-none text-uppercase" value="F"/>
                <input type="text" name="coddocrepleg" class="d-none text-uppercase" value=""/>
                <span id='component_fecsol' class="d-none"></span>
            </div>
            <div class="row">
                <div class="col-12">
                    <fieldset>
                        <legend>Datos afiliado facultativo</legend>
                        <div class="row justify-content-around">
                            <div class="col-md-3">
                                <div class="form-group" group-for='coddoc'>
                                    <label for="coddoc" class="control-label">Tipo documento:</label>
                                    <span id='component_tipdoc'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='cedtra'>
                                    <label for="cedtra" class="control-label">Identificación:</label>
                                    <span id='component_cedtra'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='priape'>
                                    <label for="priape" class="control-label">Primer apellido:</label>
                                    <span id='component_priape'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='segape'>
                                    <label for="segape" class="control-label">Segundo apellido:</label>
                                    <span id='component_segape'></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='prinom'>
                                    <label for="prinom" class="control-label">Primer nombre:</label>
                                    <span id='component_prinom'></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='segnom'>
                                    <label for="segnom" class="control-label">Segundo nombre:</label>
                                    <span id='component_segnom'></span>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group" group-for='codact'>
                                    <label for="codact" class="control-label">CIUU-DIAN Actividad economica:</label>
                                    <span id='component_codact'></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <fieldset>
                        <legend>Datos trabajador afiliado</legend>
                        <div class="row justify-content-around">

                            <div class="col-md-3">
                                <div class="form-group" group-for='fecini'>
                                    <label for="fecini" class="control-label">Fecha inicio:</label>
                                    <span id='component_fecini'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='codcaj'>
                                    <label for="codcaj" class="control-label">Caja a la que estuvo afiliado antes:</label>
                                    <span id='component_codcaj'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='fecnac'>
                                    <label for="fecnac" class="control-label">Fecha nacimiento</label>
                                    <span id='component_fecnac'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='ciunac'>
                                    <label for="ciunac" class="control-label">Ciudad nacimiento</label>
                                    <span id='component_ciunac'></span>

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='facvul'>
                                    <label for="facvul" class="control-label">Factor vulnerabilidad</label>
                                    <span id='component_facvul'></span>

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='sexo'>
                                    <label for="sexo" class="control-label">Sexo</label>
                                    <span id='component_sexo'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='orisex'>
                                    <label for="orisex" class="control-label">Orientación sexual</label>
                                    <span id='component_orisex'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='facvul'>
                                    <label for="facvul" class="control-label">Factor vulnerabilidad</label>
                                    <span id='component_facvul'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='estciv'>
                                    <label for="estciv" class="control-label">Estado civil</label>
                                    <span id='component_estciv'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='cabhog'>
                                    <label for="cabhog" class="control-label">Cabeza hogar</label>
                                    <span id='component_cabhog'></span>

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='codciu'>
                                    <label for="codciu" class="control-label">Ciudad residencia</label>
                                    <span id='component_codciu'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='direccion'>
                                    <label for="direccion" class="control-label ">Dirección de residencia</label>
                                    <span id='component_direccion'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='dirlab'>
                                    <label for="dirlab" class="control-label">Dirección de trabajo</label>
                                    <span id='component_dirlab'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='salario'>
                                    <label for="salario" class="control-label">Salario</label>
                                    <span id='component_salario'></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='tipsal'>
                                    <label for="tipsal" class="control-label">Tipo salario</label>
                                    <span id='component_tipsal'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='captra'>
                                    <label for="captra" class="control-label">Capacidad trabajo</label>
                                    <span id='component_captra'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='tipdis'>
                                    <label for="tipdis" class="control-label">Tipo discapacidad</label>
                                    <span id='component_tipdis'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='nivedu'>
                                    <label for="nivedu" class="control-label">Nivel educación</label>
                                    <span id='component_nivedu'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='vivienda'>
                                    <label for="vivienda" class="control-label">Vivienda</label>
                                    <span id='component_vivienda'></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" group-for='tipafi'>
                                    <label for="tipafi" class="control-label">Tipo afiliados</label>
                                    <span id='component_tipafi'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='peretn'>
                                    <label for="peretn" class="control-label">Pertenencia etnica</label>
                                    <span id='component_peretn'></span>
                                </div>
                            </div>

                            <div class="col-md-3 d-none show-peretn">
                                <div class="form-group" group-for='resguardo_id'>
                                    <label for="resguardo_id" class="control-label">Resguardo indigena</label>
                                    <span id='component_resguardo_id'></span>
                                </div>
                            </div>

                            <div class="col-md-3 d-none show-peretn">
                                <div class="form-group" group-for='pub_indigena_id'>
                                    <label for="pub_indigena_id" class="control-label">Pueblo indigena</label>
                                    <span id='component_pub_indigena_id'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='cargo'>
                                    <label for="cargo" class="control-label">Cargo</label>
                                    <span id='component_cargo'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='tippag'>
                                    <label for="tippag" class="control-label">Tipo pago subsidio</label>
                                    <span id='component_tippag'></span>
                                </div>
                            </div>

                            <div class="col-md-3 d-none" id='show_numcue'>
                                <div class="form-group" group-for='numcue'>
                                    <label for="numcue" class="control-label ">Número de cuenta</label>
                                    <span id='component_numcue'></span>
                                </div>
                            </div>

                            <div class="col-md-3 d-none" id='show_tipcue'>
                                <div class="form-group" group-for='tipcue'>
                                    <label for="tipcue" class="control-label">Tipo de cuenta</label>
                                    <span id='component_tipcue'></span>
                                </div>
                            </div>

                            <div class="col-md-3 d-none" id='show_codban'>
                                <div class="form-group" group-for='codban'>
                                    <label for="codban" class="control-label">Banco</label>
                                    <span id='component_codban'></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <fieldset>
                        <legend>Datos del contacto administrativo</legend>
                        <div class="row justify-content-around">
                            <div class="col-md-3">
                                <div class="form-group" group-for='email'>
                                    <label for="email" class="control-label">Email notificación</label>
                                    <span id='component_email'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='telefono'>
                                    <label for="telefono" class="control-label">Telefono notificación con indicativo:</label>
                                    <span id='component_telefono'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='celular'>
                                    <label for="celular" class="control-label">Celular notificación</label>
                                    <span id='component_celular'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='codzon'>
                                    <label for="codzon" class="control-label">Lugar donde labora:</label>
                                    <span id='component_codzon'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='ruralt'>
                                    <label for="ruralt" class="control-label">Labor rural</label>
                                    <span id='component_ruralt'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='rural'>
                                    <label for="rural" class="control-label">Residencia rural</label>
                                    <span id='component_rural'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" group-for='autoriza'>
                                    <label for="autoriza" class="control-label">Autoriza el tratamiento de datos personales</label>
                                    <span id='component_autoriza'></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

            </form>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" id='guardar_ficha'>
                        <i class="fas fa-save"></i> Guardar y continuar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">...</div>
    <div class="tab-pane fade" id="documentos_adjuntos" role="tabpanel" aria-labelledby="documentos_adjuntos-tab">...</div>
    <div class="tab-pane fade" id="firma" role="tabpanel" aria-labelledby="firma-tab">...</div>
    <div class="tab-pane fade" id="enviar_radicado" role="tabpanel" aria-labelledby="enviar_radicado-tab">
        @include('mercurio/templates.tmp_send_radicado')
    </div>
</div>
