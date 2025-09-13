
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
        @php echo Tag::form("", "id: formulario_empresa", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nit" class="form-control-label">NIT o documento empresa:</label>
                    @php echo Tag::numericField("nit", "class: form-control"); @endphp
                    @php echo Tag::numericField("id", "class: d-none"); @endphp
                    @php echo Tag::numericField("repleg", "class: d-none"); @endphp
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" style="overflow: hidden">
                    <label for="tipdoc" class="form-control-label">Tipo documento empresa:</label>
                    @php echo Tag::selectStatic("tipdoc", $_coddoc, "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                    <label id="tipdoc-error" class="error" for="tipdoc"></label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="digver" class="form-control-label">Digito verificación:</label>
                    @php echo Tag::numericField("digver", "class: form-control", "placeholder: Digito Verificacion"); @endphp
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipson" class="form-control-label">Tipo empresa:</label>
                    @php echo Tag::selectStatic("tipemp", $_tipemp, "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="tipper" class="form-control-label">Tipo persona:</label>
                    @php echo Tag::selectStatic("tipper", $_tipper, "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                    <label id="tipper-error" class="error" for="tipper"></label>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="razsoc" class="form-control-label">Razón social:</label>
                    @php echo Tag::textUpperField("razsoc", "class: form-control", "placeholder: Razon Social"); @endphp
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="sigla" class="form-control-label">Sigla:</label>
                    @php echo Tag::textUpperField("sigla", "class: form-control", "placeholder: Sigla"); @endphp
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="matmer" class="form-control-label">Mat. mercantil:</label>
                    @php echo Tag::textUpperField("matmer", "class: form-control", "placeholder: Matricula Mercantil"); @endphp
                </div>
            </div>
        </div>
        <div class="row" id='show_natural' style="display:none;">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="priape" class="form-control-label">Primer apellido:</label>
                    @php echo Tag::textUpperField("priape", "class: form-control", "placeholder: Primer Apellido"); @endphp
                    <label id="priape-error" class="error" for="priape"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segape" class="form-control-label">Segundo apellido:</label>
                    @php echo Tag::textUpperField("segape", "class: form-control", "placeholder: Segundo Apellido"); @endphp
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="prinom" class="form-control-label">Primer nombre:</label>
                    @php echo Tag::textUpperField("prinom", "class: form-control", "placeholder: Primer Nombre"); @endphp
                    <label id="prinom-error" class="error" for="prinom"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segnom" class="form-control-label">Segundo nombre:</label>
                    @php echo Tag::textUpperField("segnom", "class: form-control", "placeholder: Segundo Nombre"); @endphp
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="calemp" class="form-control-label">Calidad afiliación:</label>
                    @php echo Tag::selectStatic("calemp", $_calemp, "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="coddocrepleg" class="form-control-label">Tipo documento rep. legal:</label>
                    @php echo Tag::selectStatic("coddocrepleg", $_coddocrepleg, "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                    <label id="coddocrepleg-error" class="error" for="priaperepleg"></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cedrep" class="form-control-label">Cedula representante legal:</label>
                    @php echo Tag::textUpperField("cedrep", "class: form-control", "placeholder: Cedula representante"); @endphp
                </div>
            </div>
        </div>
        <div class="row" id='show_juridica' style="display:none;">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="priaperepleg" class="form-control-label">Primer apellido rep. legal:</label>
                    @php echo Tag::textUpperField("priaperepleg", "class: form-control", "placeholder: Primer Apellido"); @endphp
                    <label id="priaperepleg-error" class="error" for="priaperepleg"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segaperepleg" class="form-control-label">Segundo apellido rep. legal:</label>
                    @php echo Tag::textUpperField("segaperepleg", "class: form-control", "placeholder: Segundo Apellido"); @endphp
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="prinomrepleg" class="form-control-label">Primer nombre rep. legal:</label>
                    @php echo Tag::textUpperField("prinomrepleg", "class: form-control", "placeholder: Primer Nombre"); @endphp
                    <label id="prinomrepleg-error" class="error" for="prinomrepleg"></label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="segnomrepleg" class="form-control-label">Segundo nombre rep. legal:</label>
                    @php echo Tag::textUpperField("segnomrepleg", "class: form-control", "placeholder: Segundo Nombre"); @endphp
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="direccion" class="form-control-label">Direccion notificación:</label>
                    @php echo Tag::textUpperField("direccion", "class: form-control", "placeholder: Direccion"); @endphp
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="codciu" class="form-control-label">Ciudad notificación:</label>
                    @php echo Tag::selectStatic("codciu", $_codciu, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); @endphp
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
        @php echo Tag::endform(); @endphp
    </div>
</div>

<script>
    const _ID = {{ $idModel }};
</script>

<script src="{{ asset('Cajas/build/MadreComunitariaEditar.js') }}"></script>
