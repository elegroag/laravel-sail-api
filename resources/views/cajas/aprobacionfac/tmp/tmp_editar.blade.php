<script id='tmp_card_header' type="text/template">
    <div class="row">
        <div class="col-md-8">
            <h4>Ficha solicitud afiliación</h4>
            <p style='font-size:1rem'>Disponible para editar los campos del formulario digital de la solicitud.</p>
        </div>
        <div class="col-md-4">
            <div id="botones" class='row justify-content-end'>
                <a href="<%=url_salir%>" class='btn btn-sm btn-primary'><i class='fas fa-hand-point-up text-white'></i> Volver</a>&nbsp;
            </div>
        </div>
    </div>
</script>

<div class='card-header pt-2 pb-2' id='afiliacion_header'></div>

<div class="card-body">
    <div class="col-xs-12">
        <form id="formulario_empresa" class="validation_form" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nit" class="form-control-label">NIT o documento empresa:</label>
                        <input type="text" id="nit" name="nit" class="form-control" placeholder="NIT o documento empresa">
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="repleg" name="repleg">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="overflow: hidden">
                        <label for="tipdoc" class="form-control-label">Tipo documento empresa:</label>
                        <select name="tipdoc" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach($_coddoc as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label id="tipdoc-error" class="error" for="tipdoc"></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="digver" class="form-control-label">Digito verificación:</label>
                        <input type="text" id="digver" name="digver" class="form-control" placeholder="Digito Verificacion">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipson" class="form-control-label">Tipo empresa:</label>
                        <select name="tipemp" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach($_tipemp as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="tipper" class="form-control-label">Tipo persona:</label>
                        <select name="tipper" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach($_tipper as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label id="tipper-error" class="error" for="tipper"></label>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="razsoc" class="form-control-label">Razón social:</label>
                        <input type="text" id="razsoc" name="razsoc" class="form-control" placeholder="Razon Social">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="sigla" class="form-control-label">Sigla:</label>
                        <input type="text" id="sigla" name="sigla" class="form-control" placeholder="Sigla">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="matmer" class="form-control-label">Mat. mercantil:</label>
                        <input type="text" id="matmer" name="matmer" class="form-control" placeholder="Matricula Mercantil">
                    </div>
                </div>
            </div>
            <div class="row" id='show_natural' style="display:none;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="priape" class="form-control-label">Primer apellido:</label>
                        <input type="text" id="priape" name="priape" class="form-control" placeholder="Primer Apellido">
                        <label id="priape-error" class="error" for="priape"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="segape" class="form-control-label">Segundo apellido:</label>
                        <input type="text" id="segape" name="segape" class="form-control" placeholder="Segundo Apellido">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="prinom" class="form-control-label">Primer nombre:</label>
                        <input type="text" id="prinom" name="prinom" class="form-control" placeholder="Primer Nombre">
                        <label id="prinom-error" class="error" for="prinom"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="segnom" class="form-control-label">Segundo nombre:</label>
                        <input type="text" id="segnom" name="segnom" class="form-control" placeholder="Segundo Nombre">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="calemp" class="form-control-label">Calidad afiliación:</label>
                        <select name="calemp" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach($_calemp as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="coddocrepleg" class="form-control-label">Tipo documento rep. legal:</label>
                        <select name="coddocrepleg" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach($_coddocrepleg as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label id="coddocrepleg-error" class="error" for="priaperepleg"></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cedrep" class="form-control-label">Cedula representante legal:</label>
                        <input type="text" id="cedrep" name="cedrep" class="form-control" placeholder="Cedula representante">
                    </div>
                </div>
            </div>
            <div class="row" id='show_juridica' style="display:none;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="priaperepleg" class="form-control-label">Primer apellido rep. legal:</label>
                        <input type="text" id="priaperepleg" name="priaperepleg" class="form-control" placeholder="Primer Apellido">
                        <label id="priaperepleg-error" class="error" for="priaperepleg"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="segaperepleg" class="form-control-label">Segundo apellido rep. legal:</label>
                        <input type="text" id="segaperepleg" name="segaperepleg" class="form-control" placeholder="Segundo Apellido">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="prinomrepleg" class="form-control-label">Primer nombre rep. legal:</label>
                        <input type="text" id="prinomrepleg" name="prinomrepleg" class="form-control" placeholder="Primer Nombre">
                        <label id="prinomrepleg-error" class="error" for="prinomrepleg"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="segnomrepleg" class="form-control-label">Segundo nombre rep. legal:</label>
                        <input type="text" id="segnomrepleg" name="segnomrepleg" class="form-control" placeholder="Segundo Nombre">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="direccion" class="form-control-label">Direccion notificación:</label>
                        <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Direccion">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="codciu" class="form-control-label">Ciudad notificación:</label>
                        <select name="codciu" class="form-control">
                            <option value="">Seleccione un tipo de documento</option>
                            @foreach($_codciu as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label id="codciu-error" class="error" for="codciu"></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="codzon" class="form-control-label">Lugar donde laboran trabajadores:</label>
                        @php echo Tag::selectStatic("codzon", $_codzon, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); @endphp
                        <label id="codzon-error" class="error" for="codzon"></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telefono" class="form-control-label">Telefono notificación con indicativo:</label>
                        @php echo Tag::numericField("telefono", "class: form-control", "placeholder: Telefono con Indicativo"); @endphp
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="celular" class="form-control-label">Celular notificación</label>
                        @php echo Tag::numericField("celular", "class: form-control", "placeholder: Celular"); @endphp
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fax" class="form-control-label">Fax notificación</label>
                        @php echo Tag::textUpperField("fax", "class: form-control", "placeholder: Fax"); @endphp
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email" class="form-control-label">Email notificación empresarial</label>
                        @php echo Tag::textUpperField("email", "class: form-control", "placeholder: Email"); @endphp
                        <label id="email-error" class="error" for="email"></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="overflow: hidden">
                        <label for="codact" class="form-control-label">Digite el código CIUU-DIAN de la actividad economica:</label>
                        @php echo Tag::selectStatic("codact", $_codact, "class: form-control", "select2: true"); @endphp
                        <label id="codact-error" class="error" for="codact"></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecini" class="form-control-label">Fecha inicio:</label>
                        @php echo Tag::calendar("fecini", "class: form-control", "placeholder: Fecha Inicial"); @endphp
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="tottra" class="form-control-label">Total trabajadores:</label>
                        @php echo Tag::textUpperField("tottra", "class: form-control", "placeholder: Total Trabajadores"); @endphp
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="valnom" class="form-control-label">Valor nomina:</label>
                        @php echo Tag::textUpperField("valnom", "class: form-control", "placeholder: Valor Nomina"); @endphp
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipsoc" class="form-control-label">Tipo sociedad:</label>
                        @php echo Tag::selectStatic("tipsoc", $_tipsoc, "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                        <label id="tipsoc-error" class="error" for="tipsoc"></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ciupri" class="form-control-label">Ciudad comercial:</label>
                        @php echo Tag::selectStatic("ciupri", $_ciupri, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); @endphp
                        <label id="ciupri-error" class="error" for="ciupri"></label>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="celpri" class="form-control-label">Celular comercial:</label>
                        @php echo Tag::numericField("celpri", "class: form-control", "placeholder: Celular Principal"); @endphp
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="emailpri" class="form-control-label">Email comercial:</label>
                        @php echo Tag::textUpperField("emailpri", "class: form-control", "placeholder: Email Principal"); @endphp
                        <label id="emailpri-error" class="error" for="emailpri"></label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dirpri" class="form-control-label">Dirección comercial:</label>
                        @php echo Tag::textUpperField("dirpri", "class: form-control", "placeholder: dirección comercial"); @endphp
                        <label id="dirpri-error" class="error" for="dirpri"></label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telpri" class="form-control-label">Teléfono comercial:</label>
                        @php echo Tag::textUpperField("telpri", "class: form-control", "placeholder: dirección comercial"); @endphp
                        <label id="telpri-error" class="error" for="telpri"></label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-primary" id='guardar_ficha'><i class="fas fa-save"></i> Guardar los cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    const _ID = {{ $idModel }};
</script>

<script src="{{asset('cajas/build/PensionadoEditar.js')}}"></script>
