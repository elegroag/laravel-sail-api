<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="datos_solicitud" role="tabpanel" aria-labelledby="datos_solicitud-tab">
        <div class="card-body">
            <form id="formRequest" class="validation_form" autocomplete="off" novalidate>
            <div class="d-none">
                <input type="number" name="id" class="d-none">
                <input type="text" name="calemp" class="d-none" value="I">
                <input type="text" name="coddocrepleg" class="d-none" value="">
            </div>
            <div class="row">
                <div class="col-12">
                    <fieldset>
                        <legend>Datos afiliado independiente</legend>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="coddoc" class="control-label top">Tipo documento:</label>
                                    @component('components.select', [
                                        'name' => 'coddoc',
                                        'options' => $tiposDocumento,
                                        'className' => 'form-control'
                                    ])@endcomponent
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="cedtra" class="control-label">Identificación:</label>
                                    <input type="number" name="cedtra" class="form-control" placeholder="Cedula representante">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="priape" class="control-label">Primer apellido:</label>
                                    <input type="text" name="priape" class="form-control text-uppercase" placeholder="Primer Apellido">
                                    <label id="priape-error" class="error" for="priape"></label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="segape" class="control-label">Segundo apellido:</label>
                                    <input type="text" name="segape" class="form-control text-uppercase" placeholder="Segundo Apellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="prinom" class="control-label">Primer nombre:</label>
                                    <input type="text" name="prinom" class="form-control text-uppercase" placeholder="Primer Nombre">
                                    <label id="prinom-error" class="error" for="prinom"></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="segnom" class="control-label">Segundo nombre:</label>
                                    <input type="text" name="segnom" class="form-control text-uppercase" placeholder="Segundo Nombre">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group form-item">
                                    <label for="codact" class="control-label top">CIUU-DIAN Actividad economica:</label>
                                    @component('components.select', [
                                        'name' => 'codact', 
                                        'id' => 'codact', 
                                        'options' => $actividadesEconomicas,
                                        'className' => 'form-control'
                                    ])@endcomponent
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
                        <legend>Datos trabajador afiliado</legend>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="fecini" class="control-label top">Fecha inicio:</label>
                                    <input type="date" name="fecini" class="form-control" placeholder="Fecha Inicial">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="codcaj" class="control-label top">Caja a la que estuvo afiliado antes:</label>
                                    <span id='component_codcaj'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="fecnac" class="control-label top">Fecha nacimiento</label>
                                    <input type="date" name="fecnac" class="form-control" placeholder="AÑO-MES-DÍA">
                                    <label id="fecnac-error" class="error" for="fecnac"></label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="ciunac" class="control-label top">Ciudad nacimiento</label>
                                    <span id='component_ciunac'></span>

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="facvul" class="control-label top">Factor vulnerabilidad</label>
                                    <span id='component_facvul'></span>

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="sexo" class="control-label top">Sexo</label>
                                    <span id='component_sexo'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="orisex" class="control-label top">Orientación sexual</label>
                                    <span id='component_orisex'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="facvul" class="control-label top">Factor vulnerabilidad</label>
                                    <span id='component_facvul'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="estciv" class="control-label top">Estado civil</label>
                                    <span id='component_estciv'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="cabhog" class="control-label top">Cabeza hogar</label>
                                    <span id='component_cabhog'></span>

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="codciu" class="control-label top">Ciudad residencia</label>
                                    <span id='component_codciu'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="direccion" class="control-label top ml-4">Dirección de residencia</label>
                                    <input type="text" name="direccion" class="form-control" placeholder="Dirección">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="dirlab" class="control-label top ml-4">Dirección de trabajo</label>
                                    <input type="text" name="dirlab" class="form-control" placeholder="Direccion Labor">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="salario" class="control-label">Salario</label>
                                    <input type="number" name="salario" class="form-control" placeholder="Salario">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="tipsal" class="control-label top">Tipo salario</label>
                                    <span id='component_tipsal'></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="captra" class="control-label top">Capacidad trabajo</label>
                                    <span id='component_captra'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="tipdis" class="control-label top">Tipo discapacidad</label>
                                    <span id='component_tipdis'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="nivedu" class="control-label top">Nivel educación</label>
                                    <span id='component_nivedu'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="vivienda" class="control-label top">Vivienda</label>
                                    <span id='component_vivienda'></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="tipafi" class="control-label top">Tipo afiliados</label>
                                    <span id='component_tipafi'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="peretn" class="control-label top">Pertenencia etnica</label>
                                    <span id='component_peretn'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="trasin" class="control-label top">Resguardo indigena</label>
                                    <span id='component_resguardo_id'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="trasin" class="control-label top">Pueblo indigena</label>
                                    <span id='component_pub_indigena_id'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="cargo" class="control-label top">Cargo</label>
                                    <span id='component_cargo'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="tippag" class="control-label top">Tipo pago subsidio</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-sm btn-info" data-placement="top" data-bs-toggle="popover" data-content="El tipo de pago no es requerido, pero en caso de tener derecho a algun subsidio, se recomienda indicar el medio de pago y el número de cuenta." type="button" id="bt_question_tippag">
                                                <i class="fa fa-question"></i>
                                            </button>
                                        </div>
                                        <span id='component_tippag'></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" id='show_numcue'>
                                <div class="form-group form-item">
                                    <label for="tippag" class="control-label top ml-4">Número de cuenta</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button id="bt_question_tippag" class="btn btn-sm btn-info" data-placement="top" databs--toggle="popover" data-content="Para validación de la cuenta se debe de adjuntar el documento del certificado de cuenta, una vez se guarden los datos del formulario." type="button">
                                                <i class="fa fa-question"></i>
                                            </button>
                                        </div>
                                        <input type="number" name="numcue" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" id='show_numcue'>
                                <div class="form-group form-item">
                                    <label for="tippag" class="control-label top ml-4">Tipo de cuenta</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button id="bt_question_tipcue" class="btn btn-sm btn-info" data-placement="top" data-bs-toggle="popover" data-content="Es un campo requerido si selecciona una forma de pago." type="button">
                                                <i class="fa fa-question"></i>
                                            </button>
                                        </div>
                                        <span id='component_tipcue'></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" id='show_numcue'>
                                <div class="form-group form-item">
                                    <label for="tippag" class="control-label top ml-4">Banco</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button id="bt_question_codban" class="btn btn-sm btn-info" data-placement="top" data-bs-toggle="popover" data-content="Es un campo requerido si selecciona una forma de pago." type="button">
                                                <i class="fa fa-question"></i>
                                            </button>
                                        </div>
                                        <span id='component_codban'></span>
                                    </div>
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
                        <legend>Datos del contacto administrativo</legend>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="email" class="control-label">Email notificación</label>
                                    <input type="text" name="email" class="form-control" placeholder="Email">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="telefono" class="control-label">Telefono notificación con indicativo:</label>
                                    <input type="number" name="telefono" class="form-control" placeholder="Telefono con Indicativo">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="celular" class="control-label">Celular notificación</label>
                                    <input type="number" name="celular" class="form-control" placeholder="Celular">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="codzon" class="control-label top">Lugar donde labora:</label>
                                    <span id='component_codzon'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="ruralt" class="control-label top">Labor rural</label>
                                    <span id='component_ruralt'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="rural" class="control-label top">Residencia rural</label>
                                    <span id='component_rural'></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-item">
                                    <label for="autoriza" class="control-label top">Autoriza el tratamiento de datos personales</label>
                                    <span id='component_autoriza'></span>
                                </div>
                            </div>
                    </fieldset>
                </div>
            </div>

            </form>
        </div>

        @if (isset($estado) && ($estado == 'T' || $estado == 'D' || $estado == null))
        <div class="card-footer">
            <div class="col-12">
                <button type="button" class="btn btn-primary" id='guardar_ficha'>
                    <i class="fas fa-save"></i> Guardar y continuar
                </button>
            </div>
        </div>
        @endif

    </div>
    <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">...</div>
    <div class="tab-pane fade" id="documentos_adjuntos" role="tabpanel" aria-labelledby="documentos_adjuntos-tab">...</div>
    <div class="tab-pane fade" id="firma" role="tabpanel" aria-labelledby="firma-tab">...</div>
    <div class="tab-pane fade" id="enviar_radicado" role="tabpanel" aria-labelledby="enviar_radicado-tab">
        @include('mercurio.domestico.tmp_send_radicado')
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('.validation_form').validate({
        rules: {
            priape: { required: true },
            prinom: { required: true },
            cedtra: { required: true, digits: true }
        },
        messages: {
            priape: 'Debe ingresar primer apellido',
            prinom: 'Debe ingresar primer nombre',
            cedtra: {
                required: 'Debe ingresar cédula',
                digits: 'Solo números permitidos'
            }
        }
    });
});
</script>
@endsection