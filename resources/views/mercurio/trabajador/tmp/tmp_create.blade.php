@php
    use App\Services\Tag;
    $fecsol = date('Y-m-d');
@endphp
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
                <div class="d-none">
                    <input type="number" name="id" id="id" class="d-none" />
                    <input type="text" name="profesion" id="profesion" class="d-none" value="Ninguna" />
                    <input type="text" name="fax" id="fax" class="d-none" value="" />
                </div>

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos empresa y trabajador</legend>
                            <div class="row">
                                <div class="form-group d-none">
                                    <label for="fecsol" class="control-label d-none">Fecha solicitud:</label>
                                    <input class='form-control d-none' type="date" name='fecsol' id='fecsol' value="{{ $fecsol }}">
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='nit'>
                                        <label for='nit' class='control-label'>NIT</label>
                                        @php echo $input_nits @endphp
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='razsoc'>
                                        <label for='razsoc' class='control-label'>Razón social</label>
                                        @php echo $input_razsoc @endphp
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='codsuc'>
                                        <label for="codsuc" class="control-label">Sucursal empresa</label>
                                        <span id='component_codsuc'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipdoc'>
                                        <label for="tipdoc" class="control-label">Tipo documento trabajador</label>
                                        <span id='component_tipdoc'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='cedtra'>
                                        <label for="cedtra" class="control-label">Número identificación</label>
                                        <input type="number" name="cedtra" id="cedtra" class="form-control" placeholder="Identificación" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='priape'>
                                        <label for="priape" class="control-label">Primer apellido</label>
                                        <input type="text" name="priape" id="priape" class="form-control" placeholder="Primer Apellido" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='segape'>
                                        <label for="segape" class="control-label">Segundo apellido</label>
                                        <input type="text" name="segape" id="segape" class="form-control" placeholder="Segundo Apellido" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='prinom'>
                                        <label for="prinom" class="control-label">Primer nombre</label>
                                        <input type="text" name="prinom" id="prinom" class="form-control" placeholder="Primer Nombre" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='segnom'>
                                        <label for="segnom" class="control-label">Segundo nombre</label>
                                        <input type="text" name="segnom" id="segnom" class="form-control" placeholder="Segundo Nombre" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='codzon'>
                                        <label for="codzon" class="control-label">Zona trabajo</label>
                                        <span id='component_codzon'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='dirlab'>
                                        @component('components/address', [
                                            'name' => 'dirlab', 
                                            'value' => '',
                                            'placeholder' => 'Dirección de trabajo',
                                            'event' => 'address',
                                            'label' => 'Dirección de trabajo'
                                        ])@endcomponent
                                    </div>
                                </div>

                                <div class="col-md-3 ">
                                    <div class='form-group' group-for='ruralt'>
                                        <label for="ruralt" class="control-label">Labor rural</label>
                                        <span id='component_ruralt'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='fecing'>
                                        <label for="fecing" class="control-label">Fecha ingreso <small>(AÑO-MES-DÍA)</small></label>
                                        <input type="date" name="fecing" id="fecing" class="form-control" placeholder="Fecha Ingreso" />
                                        <label id="fecing-error" class="error" for="fecing"></label>
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
                            <legend>Datos basicos trabajador</legend>
                            <div class="row">

                                <div class="col-md-3">
                                    <div class='form-group' group-for='fecnac'>
                                        <label for="fecnac" class="control-label">Fecha nacimiento <small>(AÑO-MES-DÍA)</small></label>
                                        <input type="date" name="fecnac" id="fecnac" class="form-control" />
                                        <label id="fecnac-error" class="error" for="fecnac"></label>
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
                                    <div class='form-group' group-for='orisex'>
                                        <label for="orisex" class="control-label">Orientación sexual</label>
                                        <span id='component_orisex'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='facvul'>
                                        <label for="facvul" class="control-label">Factor vulnerabilidad</label>
                                        <span id='component_facvul'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='estciv'>
                                        <label for="estciv" class="control-label">Estado civil</label>
                                        <span id='component_estciv'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='cabhog'>
                                        <label for="cabhog" class="control-label">Cabeza hogar</label>
                                        <span id='component_cabhog'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='peretn'>
                                        <label for="peretn" class="control-label">Pertenencia etnica</label>
                                        <span id='component_peretn'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='captra'>
                                        <label for="captra" class="control-label">Capacidad trabajo</label>
                                        <span id='component_captra'></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class='form-group' group-for='tipdis'>
                                        <label for="tipdis" class="control-label">Tipo discapacidad</label>
                                        <span id='component_tipdis'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='tippag'>
                                        <label for="tippag" class="control-label ">Tipo pago subsidio</label>
                                        <span id='component_tippag'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id="show_numcue">
                                    <div class='form-group' group-for='numcue'>
                                        <label for="numcue" class="control-label">Número de cuenta</label>
                                        <input type="number" name="numcue" id="numcue" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id="show_codban">
                                    <div class='form-group' group-for='codban'>
                                        <label for="codban" class="control-label">Banco</label>
                                        <span id='component_codban'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id='show_tipcue'>
                                    <div class='form-group' group-for='tipcue'>
                                        <label for="tipcue" class="control-label">Tipo de cuenta</label>
                                        <span id='component_tipcue'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='trasin'>
                                        <label for="trasin" class="control-label">Sindicalizado</label>
                                        <span id='component_trasin'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='nivedu'>
                                        <label for="nivedu" class="control-label">Nivel educación</label>
                                        <span id='component_nivedu'></span>
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos requeridos afiliación</legend>
                            <div class="row">

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipafi'>
                                        <label for="tipafi" class="control-label">Tipo afiliados</label>
                                        <span id='component_tipafi'></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class='form-group' group-for='cargo'>
                                        <label for="cargo" class="control-label">Cargo</label>
                                        <span id='component_cargo'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='salario'>
                                        <label for="salario" class="control-label">Salario</label>
                                        <input type="text" name="salario" id="salario" class="form-control" placeholder="Salario" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipsal'>
                                        <label for="tipsal" class="control-label">Tipo salario</label>
                                        <span id='component_tipsal'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='horas'>
                                        <label for="horas" class="control-label">Horas (MENSUAL)</label>
                                        <input type="text" name="horas" id="horas" class="form-control" placeholder="Horas" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipcon'>
                                        <label for="tipcon" class="control-label">Tipo contrato</label>
                                        <span id='component_tipcon'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='tipjor'>
                                        <label for="tipjor" class="control-label">Tipo jornada laboral</label>
                                        <span id='component_tipjor'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='comision'>
                                        <label for="comision" class="control-label">Recibe comisión</label>
                                        <span id='component_comision'></span>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class='form-group' group-for='labora_otra_empresa'>
                                        <label for="labora_otra_empresa" class="control-label">Labora en otra empresa:</label>
                                        <span id='component_labora_otra_empresa'></span>
                                    </div>
                                </div>

                                <div id='show_otra_empresa' class="col-md-3 d-none">
                                    <div class='form-group' group-for='otra_empresa'>
                                        <label for="otra_empresa" class="control-label">Cual empresa labora:</label>
                                        <input type="text" name="otra_empresa" id="otra_empresa" class="form-control" placeholder="Nombre empresa" />
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
                                <div class="col-md-3 ">
                                    <div class='form-group' group-for='telefono'>
                                        <label for="telefono" class="control-label">Teléfono</label>
                                        <input type="number" name="telefono" id="telefono" class="form-control" placeholder="Telefono" maxlength="10" minlength="10" />
                                    </div>
                                </div>

                                <div class="col-md-3 ">
                                    <div class='form-group' group-for='celular'>
                                        <label for="celular" class="control-label">Celular</label>
                                        <input type="number" name="celular" id="celular" class="form-control" placeholder="Celular" maxlength="10" minlength="10" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='codciu'>
                                        <label for="codciu" class="control-label">Ciudad residencia</label>
                                        <span id='component_codciu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='direccion'>
                                        @component('components/address', [
                                            'name' => 'direccion', 
                                            'value' => '',
                                            'placeholder' => 'Dirección de residencia',
                                            'event' => 'address',
                                            'label' => 'Dirección de residencia'
                                        ])@endcomponent
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='barrio'>
                                        <label for="barrio" class="control-label">Barrio de residencia</label>
                                        <input type="text" name="barrio" id="barrio" class="form-control" placeholder="Barrio" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class='form-group' group-for='email'>
                                        <label for="email" class="control-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" />
                                    </div>
                                </div>

                                <div class="col-md-3 ">
                                    <div class='form-group' group-for='rural'>
                                        <label for="rural" class="control-label">Residencia rural</label>
                                        <span id='component_rural'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 ">
                                    <div class='form-group' group-for='vivienda'>
                                        <label for="vivienda" class="control-label">Vivienda</label>
                                        <span id='component_vivienda'></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class='form-group' group-for='autoriza'>
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
            <div class="col-12">
                <% if (estado == 'T' || estado == 'D' || estado == void 0) { %>
                <button type="button" class="btn btn-primary" id='guardar_ficha'>
                    <i class="fas fa-save"></i> Guardar
                </button>
                <% }else{ %>
                <p>Solicitud en estado pendiente de validación.</p>
                <%} %>
            </div>
        </div>

    </div>
    <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">...</div>
    <div class="tab-pane fade" id="documentos_adjuntos" role="tabpanel" aria-labelledby="documentos_adjuntos-tab">...</div>
    <div class="tab-pane fade" id="firma" role="tabpanel" aria-labelledby="firma-tab">...</div>
    <div class="tab-pane fade" id="enviar_radicado" role="tabpanel" aria-labelledby="enviar_radicado-tab">
        @include('mercurio/templates/tmp_send_radicado')
    </div>
</div>
