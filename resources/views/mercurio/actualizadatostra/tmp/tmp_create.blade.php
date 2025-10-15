<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
                @csrf
                <div class="d-none">
                    <input type="number" name="cedtra" class="d-none" value="{{ $cedtra }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    <input type="number" name="id" class="d-none">
                    <input type="number" name="tipo_actualizacion" class="d-none" value="T">
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <fieldset>
                            <legend>Datos Basicos Trabajador</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="prinom" class="control-label">Primer nombre</label>
                                        <input type="text" name="prinom" class="form-control" placeholder="Primer Nombre" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="segnom" class="control-label">Segundo nombre</label>
                                        <x-inputs.text name="segnom" placeholder="Segundo Nombre" oninput="this.value = this.value.toUpperCase()"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="priape" class="control-label">Primer apellido</label>
                                        <x-inputs.text name="priape" placeholder="Primer Apellido" oninput="this.value = this.value.toUpperCase()"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="segape" class="control-label">Segundo apellido</label>
                                        <x-inputs.text name="segape" placeholder="Segundo Apellido" oninput="this.value = this.value.toUpperCase()"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="expedicion" class="control-label top">Fecha expedición documento</label>
                                        <input type="date" name="expedicion" class="form-control" placeholder="Fecha expedición">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="telefono" class="control-label">Teléfono</label>
                                        <x-inputs.number name="telefono" placeholder="Telefono" maxlength="10" minlength="10" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="celular" class="control-label">Celular</label>
                                        <x-inputs.number name="celular" placeholder="Celular" maxlength="10" minlength="10" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="codciu" class="control-label top">Ciudad residencia</label>
                                        <span id='component_codciu'></span>
                                        <label id="codciu-error" class="error" for="codciu"></label>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
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
                                    <div class="form-group form-item">
                                        <label for="codzon" class="control-label top">Zona trabajo</label>
                                        <span id='component_codzon'></span>
                                        <label id="codzon-error" class="error" for="codzon"></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                         @component('components/address', [
                                            'name' => 'dirlab', 
                                            'value' => '',
                                            'placeholder' => 'Dirección laboral',
                                            'event' => 'address',
                                            'label' => 'Dirección laboral'
                                        ])@endcomponent
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="email" class="control-label">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Email" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <fieldset>
                            <legend>Información General Responsable Cuota Monetaria</legend>
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="tipdoc" class="control-label top">Tipo documento responsable</label>
                                        <span id='component_respo_tipdoc'></span>
                                    </div>
                                </div>

                                <div class="col-md-3 col-lg-2">
                                    <div class="form-group form-item">
                                        <label for="cedtra" class="control-label">Número identificación responsable</label>
                                        <input type="number" name="respo_cedtra" class="form-control" placeholder="Identificación" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_prinom" class="control-label">Primer nombre responsable</label>
                                        <input type="text" name="respo_prinom" class="form-control" placeholder="Primer Nombre" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_segnom" class="control-label">Segundo nombre responsable</label>
                                        <input type="text" name="respo_segnom" class="form-control" placeholder="Segundo Nombre" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_priape" class="control-label">Primer apellido responsable</label>
                                        <input type="text" name="respo_priape" class="form-control" placeholder="Primer Apellido" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_segape" class="control-label">Segundo apellido responsable</label>
                                        <input type="text" name="respo_segape" class="form-control" placeholder="Segundo Apellido" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_expedicion" class="control-label">Fecha expedición documento</label>
                                        <input type="date" name="respo_expedicion" class="form-control" placeholder="Fecha expedición">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_telefono" class="control-label">Teléfono</label>
                                        <x-inputs.number name="respo_telefono" placeholder="Telefono" maxlength="10" minlength="10" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_celular" class="control-label">Celular</label>
                                        <x-inputs.number name="respo_celular" placeholder="Celular" maxlength="10" minlength="10" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-item">
                                        <label for="respo_email" class="control-label">Email</label>
                                        <input type="email" name="respo_email" class="form-control" placeholder="Email" oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <fieldset>
                            <legend>Medio De Pago Cuota Monetaria</legend>
                            <p>Los siguientes datos deben estar asociados a la persona responsable que dispone del beneficio de cuota monetaria.</p>
                            <div class="row pb-3">
                                <div class="col-md-3">
                                    <label for="tippag" class="control-label top ml-3">Tipo pago subsidio</label>
                                    <div class="input-group mb-0">
                                        <span id='component_tippag'></span>
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-info" data-placement="top" data-bs-toggle="popover" data-content="El tipo de pago no es requerido, pero en caso de tener derecho a algun subsidio,
                                                se recomienda indicar el medio de pago y el número de cuenta." type="button" id="bt_question_tippag">
                                                <i class="fa fa-question"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <label id="tippag-error" class="error" for="tippag"></label>
                                </div>

                                <div class="col-md-3" id="show_numcue">
                                    <div class="form-group form-item">
                                        <label for="numcue" class="control-label">Número de cuenta</label>
                                        <x-inputs.number name="numcue" placeholder="Número de cuenta" />
                                    </div>
                                </div>

                                <div class="col-md-3" id="show_codban">
                                    <div class="form-group form-item">
                                        <label for="codban" class="control-label top">Banco</label>
                                        <span id='component_codban'></span>
                                    </div>
                                </div>

                                <div class="col-md-3" id='show_tipcue'>
                                    <div class="form-group form-item">
                                        <label for="tipcu" class="control-label top">Tipo de cuenta</label>
                                        <span id='component_tipcue'></span>
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
        @include('mercurio/templates/tmp_send_radicado')
    </div>
</div>
