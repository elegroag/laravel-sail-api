@php
    use App\Services\Tag;
@endphp

<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
                <div class="d-none">
                    <input type="number" name="id" class="d-none" event="is_numeric">
                    <input type="text" name="profesion" class="d-none" value="Ninguna">
                    <input type="text" name="fax" class="d-none" value="18001">
                </div>

                <div class="row mb-2">
                    <div class="col">
                        <fieldset>
                            <legend>Datos relación trabajador - conyuge</legend>
                            <div class="row">
                                @if($tipo == 'E')
                                    <div class="col-md-3">
                                        <div class="form-group" group-for='cedtra'>
                                            <label for="cedtra" class="control-label">Cedula trabajador</label>
                                            <input type="number" name="cedtra" class="form-control" placeholder="Cedula trabajador" maxlength="18" minlength="5" oninput="this.value=this.value.replace(/[^0-9]/g,'')" event="is_numeric">
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-group" group-for='cedtra'>
                                            <label for="cedtra" class="control-label">Identificación del trabajador</label>
                                            <input type="number" name="cedtra" class="form-control" readonly value="{{ $documento }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" event="is_numeric">
                                        </div>
                                    </div>
                                @endif

                                @if($tipo == 'E')
                                    <div class="col-md-3">
                                        <div class='form-group' group-for='nit'>
                                            <label class='control-label'>NIT</label>
                                            <input type="number" name="nit" class="form-control" readonly value="{{ $documento }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" event="is_numeric">
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-3">
                                    <div class="form-group" group-for='comper'>
                                        <label for="comper" class="control-label">Compañer@ permanente</label>
                                        <span id='component_comper'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tiecon'>
                                        <label for="tiecon" class="control-label">Tiempo convivencia (Año)</label>
                                        <div class="form-group" group-for='tiecon'>
                                            <label for="tiecon" class="control-label">Tiempo convivencia (Año)</label>
                                            <input type="number" name="tiecon" class="form-control" placeholder="Tiempo de Convivencia" maxlength="3" minlength="1" type="number" event="is_numeric">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='ciures'>
                                        <label for="ciures" class="control-label">Ciudad residencia</label>
                                        <span id='component_ciures'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='codzon'>
                                        <label for="codzon" class="control-label">Zona</label>
                                        <span id='component_codzon'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tipviv'>
                                        <label for="tipviv" class="control-label">Vivienda</label>
                                        <span id='component_tipviv'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='direccion'>
                                        <label for="direccion" class="control-label ml-4">Dirección de residencia</label>
                                        @php echo Tag::addressField("direccion", "class: form-control", "placeholder: dirección", "event: address"); @endphp
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='nivedu'>
                                        <label for="nivedu" class="control-label">Nivel educación</label>
                                        <span id='component_nivedu'></span>
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
                                    <div class="form-group" group-for='codocu'>
                                        <label for="codocu" class="control-label">Ocupación</label>
                                        <span id='component_codocu'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id='show_empresalab'>
                                    <div class="form-group" group-for='empresalab'>
                                        <label for="empresalab" class="control-label">Empresa donde labora</label>
                                        <input type="text" name="empresalab" placeholder="nombre empresa" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id='show_fecing'>
                                    <div class="form-group" group-for='fecing'>
                                        <label for="fecing" class="control-label">Fecha inicio laboral </label>
                                        <span>
                                            <input type="date" name="fecing" class="form-control" placeholder="Fecha Ingreso">
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id='show_salario'>
                                    <div class="form-group" group-for='salario'>
                                        <label for="salario" class="control-label">Ingresos mensuales</label>
                                        <input type="number" name="salario" class="form-control" placeholder="Salario" type="number" event="is_numeric">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='tippag'>
                                        <label for="tippag" class="control-label">Tipo pago subsidio</label>
                                        <span id='component_tippag'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id="show_numcue">
                                    <label for="numcue" class="control-label">Número de cuenta</label>
                                    <div class="input-group mb-0" group-for='numcue'>
                                        <input type="number" name="numcue" class="form-control" event="is_numeric">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-info" data-placement="top" data-bs-toggle="popover" data-content="Para validación de la cuenta se debe de adjuntar el documento del certificado de cuenta,
                                                una vez se guarden los datos del formulario." type="button" id="bt_question_numcue">
                                                <i class="fa fa-question"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none" id="show_codban">
                                    <div class="form-group" group-for='codban'>
                                        <label for="codban" class="control-label">Banco</label>
                                        <span id='component_codban'></span>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group" group-for='autoriza'>
                                        <label for="autoriza" class="control-label">Autoriza el tratamiento de datos personales</label>
                                        <span id='component_autoriza'></span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col">
                        <fieldset>
                            <legend>Datos basicos conyuge</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" group-for='tipdoc'>
                                        <label for="tipdoc" class="control-label">Tipo documento conyuge</label>
                                        <span id='component_tipdoc'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='cedcon'>
                                        <label for="cedcon" class="control-label">Número identificación</label>
                                        <input type="number" name="cedcon" class="form-control" type="number" placeholder="Identificación" event="is_numeric">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='priape'>
                                        <label for="priape" class="control-label">Primer apellido</label>
                                        <input type="text" name="priape" class="form-control" placeholder="Primer Apellido" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='segape'>
                                        <label for="segape" class="control-label">Segundo apellido</label>
                                        <input type="text" name="segape" class="form-control" placeholder="Segundo Apellido" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='prinom'>
                                        <label for="prinom" class="control-label">Primer nombre</label>
                                        <input type="text" name="prinom" class="form-control" placeholder="Primer Nombre" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='segnom'>
                                        <label for="segnom" class="control-label">Segundo nombre</label>
                                        <input type="text" name="segnom" class="form-control" placeholder="Segundo Nombre" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='fecnac'>
                                        <label for="fecnac" class="control-label">Fecha nacimiento <small>(AÑO-MES-DÍA)</small></label>
                                        <input type="date" name="fecnac" class="form-control" />
                                        <label id="fecnac-error" class="error" for="fecnac"></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='ciunac'>
                                        <label for="ciunac" class="control-label">Ciudad nacimiento</label>
                                        <span id='component_ciunac'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='sexo'>
                                        <label for="sexo" class="control-label">Sexo</label>
                                        <span id='component_sexo'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='estciv'>
                                        <label for="estciv" class="control-label">Estado civil</label>
                                        <span id='component_estciv'></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" group-for='telefono'>
                                        <label for="telefono" class="control-label">Teléfono</label>
                                        <input type="number" name="telefono" class="form-control" placeholder="Telefono" maxlength="10" minlength="10" type="number" event="is_numeric">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='celular'>
                                        <label for="celular" class="control-label">Celular</label>
                                        <input type="number" name="celular" class="form-control" placeholder="Celular" maxlength="10" minlength="10" type="number" event="is_numeric">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='email'>
                                        <label for="email" class="control-label">Email</label>
                                        <input type="text" name="email" class="form-control uppercase" placeholder="Email">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='peretn'>
                                        <label for="peretn" class="control-label">Pertenencia etnica</label>
                                        <span id='component_peretn'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='esguardo_id'>
                                        <label for="esguardo_id" class="control-label">Resguardo indigena</label>
                                        <span id='component_resguardo_id'></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" group-for='pub_indigena_id'>
                                        <label for="pub_indigena_id" class="control-label">Pueblo indigena</label>
                                        <span id='component_pub_indigena_id'></span>
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>

            </form>

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
    </div>
    
    <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">...</div>
    <div class="tab-pane fade" id="documentos_adjuntos" role="tabpanel" aria-labelledby="documentos_adjuntos-tab">...</div>
    <div class="tab-pane fade" id="firma" role="tabpanel" aria-labelledby="firma-tab">...</div>

    <div class="tab-pane fade" id="enviar_radicado" role="tabpanel" aria-labelledby="enviar_radicado-tab">
        @include('mercurio/templates/tmp_send_radicado')
    </div>
</div>
