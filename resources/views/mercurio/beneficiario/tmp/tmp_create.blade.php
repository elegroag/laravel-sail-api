<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
                @csrf
                <div class="d-none">
                    <input type="number" name="id" class="d-none" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    <input type="text" name="profesion" class="d-none" value="Ninguna" oninput="this.value = this.value.toUpperCase()">
                    <input type="text" name="fax" class="d-none" value="" oninput="this.value = this.value.toUpperCase()">
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos relación beneficiario - trabajador</legend>
                            <div class="row justify-content-around">
                                <div class="col-md-3">
                                    <div class='form-group' group-for='nit'>
                                        <label class='control-label'>NIT empresa</label>
                                        <span id='component_nit'></span>
                                    </div>
                                </div>
                            
                                <div class="col-md-3">
                                    <div class='form-group' group-for='parent'>
                                        <label for="parent" class="control-label">Parentesco con trabajador</label>
                                        <span id='component_parent'></span>
                                    </div>
                                </div>

              
                                <div class="col-md-3">
                                    <div class='form-group' group-for='cedtra'>
                                        <label for="cedtra" class="control-label">Cedula trabajador</label>
                                        <span id='component_cedtra'></span>
                                    </div>
                                </div>

                                <div class="col-md-3  d-none" id="show_mother">
                                    <div class='form-group' group-for='cedcon'>
                                        <label for="cedcon" class="control-label">Identificación (madre/padre) diferente trabajador</label>
                                        <span id='component_cedcon'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='convive'>
                                        <label for="convive" class="control-label">Con quien convive:</label>
                                        <span id='component_convive'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='cedacu'>
                                        <label for="cedacu" class="control-label">Identificación convive:</label>
                                        <span id='component_cedacu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='huerfano'>
                                        <label for="huerfano" class="control-label">Es huerfano</label>
                                        <span id='component_huerfano'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tiphij'>
                                        <label for="tiphij" class="control-label">Tipo hijo</label>
                                        <span id='component_tiphij'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='peretn'>
                                        <label for="peretn" class="control-label">Pertenencia etnica</label>
                                        <span id='component_peretn'></span>
                                    </div>
                                </div>

                                <div class="col-md-3  show-peretn">
                                    <div class='form-group' group-for='trasin'>
                                        <label for="trasin" class="control-label">Resguardo indigena</label>
                                        <span id='component_resguardo_id'></span>
                                    </div>
                                </div>

                                <div class="col-md-3  show-peretn">
                                    <div class='form-group' group-for='trasin'>
                                        <label for="trasin" class="control-label">Pueblo indigena</label>
                                        <span id='component_pub_indigena_id'></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos basicos beneficiario</legend>
                            <div class="row justify-content-around">
                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipdoc'>
                                        <label for="tipdoc" class="control-label">Tipo documento beneficiario</label>
                                        <span id='component_tipdoc'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='numdoc'>
                                        <label for="numdoc" class="control-label">Número identificación</label>
                                        <span id='component_numdoc'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='priape'>
                                        <label for="priape" class="control-label">Primer apellido</label>
                                        <span id='component_priape'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='segape'>
                                        <label for="segape" class="control-label">Segundo apellido</label>
                                        <span id='component_segape'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='prinom'>
                                        <label for="prinom" class="control-label">Primer nombre</label>
                                        <span id='component_prinom'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for=''>
                                        <label for="segnom" class="control-label">Segundo nombre</label>
                                        <span id='component_segnom'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='fecnac'>
                                        <label for="fecnac" class="control-label">Fecha nacimiento <small>(AÑO-MES-DÍA)</small></label>
                                        <span id='component_fecnac'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='ciunac'>
                                        <label for="ciunac" class="control-label">Ciudad nacimiento</label>
                                        <span id='component_ciunac'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='sexo'>
                                        <label for="sexo" class="control-label">Sexo</label>
                                        <span id='component_sexo'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='nivedu'>
                                        <label for="nivedu" class="control-label">Nivel educativo</label>
                                        <span id='component_nivedu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='captra'>
                                        <label for="captra" class="control-label">Capacidad de trabajar:</label>
                                        <span id='component_captra'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipdis'>
                                        <label for="tipdis" class="control-label">Tipo discapacidad:</label>
                                        <span id='component_tipdis'></span>
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Derecho a subsidio cuota monetaria</legend>
                            <h6>¡Tenga encuenta por favor!</h6>
                            <p>Por politicas internas de la Caja de compensación del Caquetá COMFACA, el beneficio de cuota monetaria solo se paga a la mamá del hijo que este dentro o fuera del nucleo familiar del trabajador.<br />
                                En caso que el beneficiario no sea hijo o hijastro el beneficio se paga al trabajador.<br />
                                En caso tal que el trabajador sea el papá del menor y este disponga la custodia legal del hijo o hijastro tambien puede acceder al beneficio.<br />
                                Los siguientes datos permiten comprobar si el beneficiario tiene derecho al beneficio de Subsidio de cuota monetaria.</p>

                            <div class="row show-biologico justify-content-around">
                                <div class="col-12">
                                    <label>Datos de padre/madre biológico diferente al trabajador:</label>
                                </div>
                                <div class="col-12">
                                    <div class='form-group' group-for='biodesco' style="display:inline-block;">
                                        <label class='label' style="display:inline-block;">¿Desconoce ubicación padre/madre biológico ?</label>
                                        <span style="display:inline-block;" id='component_biodesco'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='biocedu'>
                                        <label class='control-label'>Cedula padre/madre biológico</label>
                                        <span id='component_biocedu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='biotipdoc'>
                                        <label class='control-label'>Tipo documento padre/madre biológico</label>
                                        <span id='component_biotipdoc'></span>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class='form-group' group-for='bioprinom'>
                                        <label class='control-label'>Primer nombre padre/madre biológico</label>
                                        <span id='component_bioprinom'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 ">
                                    <div class='form-group' group-for='biosegnom'>
                                        <label class='control-label'>Segundo nombre padre/madre biológico</label>
                                        <span id='component_biosegnom'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='biopriape'>
                                        <label class='control-label'>Primer apellido padre/madre biológico</label>
                                        <span id='component_biopriape'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='biosegape'>
                                        <label class='control-label'>Segundo apellido padre/madre biológico</label>
                                        <span id='component_biosegape'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 s-bio-desco">
                                    <div class='form-group' group-for='bioemail'>
                                        <label class='control-label'>Email padre/madre biológico</label>
                                        <span id='component_bioemail'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 s-bio-desco">
                                    <div class='form-group' group-for='biophone'>
                                        <label class='control-label'>Teléfono padre/madre biológico</label>
                                        <span id='component_biophone'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 s-bio-desco">
                                    <div class='form-group' group-for='biocodciu'>
                                        <label class='control-label'>Ciudad residencia padre/madre biológico</label>
                                        <span id='component_biocodciu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3  s-bio-desco">
                                    <div class='form-group' group-for='biodire'>
                                        <label class='control-label'>Dirección residencia padre/madre biológico</label>
                                        <span id='component_biodire'></span>
                                    </div>
                                </div>

                                <div class="col-md-3  s-bio-desco">
                                    <div class='form-group' group-for='biourbana'>
                                        <label class='control-label'>Residencia zona urbana</label>
                                        <span id='component_biourbana'></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos de medio de pago subsidio cuota monetaria</legend>
                            <div class="row justify-content-around">
                                <div class="col-12">
                                    <h6>¡Tenga encuenta por favor!</h6>
                                    <p>El medio de pago y cuenta relacionada, debe estar subscrito al trabajador o conyuge madre del menor, quien posee el beneficio y derecho al subsidio de cuota monetaria.</p>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tippag'>
                                        <label class='control-label'>Tipo medio pago Subsidio</label>
                                        <span id='component_tippag'></span>
                                    </div>
                                </div>

                                <div class="col-md-3" id='show_numcue'>
                                    <div class='form-group' group-for='numcue'>
                                        <label class='control-label'>Número de cuenta o Daviplata</label>
                                        <span id='component_numcue'></span>
                                    </div>
                                </div>

                                <div class="col-md-3" id='show_tipcue'>
                                    <div class='form-group' group-for='tipcue'>
                                        <label class='control-label'>Tipo de cuenta</label>
                                        <span id='component_tipcue'></span>
                                    </div>
                                </div>

                                <div class="col-md-3" id='show_codban'>
                                    <div class='form-group' group-for='codban'>
                                        <label class='control-label'>Banco</label>
                                        <span id='component_codban'></span>
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-footer">
            <% if (estado == 'T' || estado == 'D' || estado == void 0) { %>
                <div class="row justify-content-center">
                    <div class="col">
                        <button type="button" class="btn btn-primary" id='guardar_ficha'>
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
        @include('mercurio/templates/tmp_send_radicado')
    </div>
</div>
