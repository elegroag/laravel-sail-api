@extends('layouts.dash')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/mercurio/mercurio.css') }}">
@endpush

@section('content')

<script id='tmp_card_header' type="text/template">
    <div class="row">
        <div class="col-md-10">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" id="datos_empresa_tab" href="#datos_empresa" aria-controls="datos_empresa" aria-selected="true">Paso 1 Datos empresa</a>
                </li>
                <li class="nav-item <%=mostrar_items%>">
                    <a class="nav-link" data-bs-toggle="tab" id="archivos_empresa_tab" href="#documentos_adjuntos" aria-controls="documentos_adjuntos" aria-selected="true">Paso 2 Adjuntar Documentos</a>
                </li>
                <li class="nav-item <%=mostrar_items%>">
                    <a class="nav-link bg-success text-white" id='enviarCaja' href="#">Paso 3 <i class='fa fas fa-upload'></i>  <b>Enviar radicado</b></a>
                </li>    
            </ul>        
        </div>
        <div class="col-md-2">
            <div id="botones" class='row justify-content-end'>
                <a href="<%=url_salir%>" class='btn btn-sm btn-primary'><i class='fas fa-hand-point-up text-white'></i> Salir</a>&nbsp;
            </div>  
        </div>
    </div>
</script>


<div class='card-header' id='afiliacion_header'></div>

<div class="card-body">
    <div class="col-xs-12">
        <div class="tab-content" id="my_tab_content">
            <div class="tab-pane fade show active" id="datos_empresa" role="tabpanel" aria-labelledby="datos_empresa_tab">
                <?= Tag::form("", "id: formulario_empresa", "class: validation_form", "autocomplete: off", "novalidate"); ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nit" class="form-control-label">NIT o documento empresa:</label>
                            <?= Tag::numericField("nit", "class: form-control"); ?>
                            <?= Tag::numericField("id", "class: d-none"); ?>
                            <?= Tag::numericField("repleg", "class: d-none"); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="overflow: hidden">
                            <label for="tipdoc" class="form-control-label">Tipo documento empresa:</label>
                            <?= Tag::selectStatic("tipdoc", $_coddoc, "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                            <label id="tipdoc-error" class="error" for="tipdoc"></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="digver" class="form-control-label">Digito verificación:</label>
                            <?= Tag::numericField("digver", "class: form-control", "placeholder: Digito Verificacion"); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipson" class="form-control-label">Tipo empresa:</label>
                            <?= Tag::selectStatic("tipemp", $_tipemp, "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tipper" class="form-control-label">Tipo persona:</label>
                            <?= Tag::selectStatic("tipper", $_tipper, "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="razsoc" class="form-control-label">Razón social:</label>
                            <?= Tag::textUpperField("razsoc", "class: form-control", "placeholder: Razon Social"); ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sigla" class="form-control-label">Sigla:</label>
                            <?= Tag::textUpperField("sigla", "class: form-control", "placeholder: Sigla"); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="matmer" class="form-control-label">Mat. mercantil:</label>
                            <?= Tag::textUpperField("matmer", "class: form-control", "placeholder: Matricula Mercantil"); ?>
                        </div>
                    </div>
                </div>
                <div class="row" id='show_natural' style="display:none;">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priape" class="form-control-label">Primer apellido:</label>
                            <?= Tag::textUpperField("priape", "class: form-control", "placeholder: Primer Apellido"); ?>
                            <label id="priape-error" class="error" for="priape"></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="segape" class="form-control-label">Segundo apellido:</label>
                            <?= Tag::textUpperField("segape", "class: form-control", "placeholder: Segundo Apellido"); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="prinom" class="form-control-label">Primer nombre:</label>
                            <?= Tag::textUpperField("prinom", "class: form-control", "placeholder: Primer Nombre"); ?>
                            <label id="prinom-error" class="error" for="prinom"></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="segnom" class="form-control-label">Segundo nombre:</label>
                            <?= Tag::textUpperField("segnom", "class: form-control", "placeholder: Segundo Nombre"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="calemp" class="form-control-label">Calidad empresa:</label>
                            <?= Tag::selectStatic("calemp", $_calemp, "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="coddocrepleg" class="form-control-label">Tipo documento rep. legal:</label>
                            <?= Tag::selectStatic("coddocrepleg", $_coddoc, "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                            <label id="coddocrepleg-error" class="error" for="coddocrepleg"></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cedrep" class="form-control-label">Cedula representante legal:</label>
                            <?= Tag::textUpperField("cedrep", "class: form-control", "placeholder: Cedula representante"); ?>
                        </div>
                    </div>
                </div>
                <div class="row" id='show_juridica' style="display:none;">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priaperepleg" class="form-control-label">Primer apellido rep. legal:</label>
                            <?= Tag::textUpperField("priaperepleg", "class: form-control", "placeholder: Primer Apellido"); ?>
                            <label id="priaperepleg-error" class="error" for="priaperepleg"></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="segaperepleg" class="form-control-label">Segundo apellido rep. legal:</label>
                            <?= Tag::textUpperField("segaperepleg", "class: form-control", "placeholder: Segundo Apellido"); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="prinomrepleg" class="form-control-label">Primer nombre rep. legal:</label>
                            <?= Tag::textUpperField("prinomrepleg", "class: form-control", "placeholder: Primer Nombre"); ?>
                            <label id="prinomrepleg-error" class="error" for="prinomrepleg"></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="segnomrepleg" class="form-control-label">Segundo nombre rep. legal:</label>
                            <?= Tag::textUpperField("segnomrepleg", "class: form-control", "placeholder: Segundo Nombre"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="direccion" class="form-control-label">Direccion notificación:</label>
                            <?= TagUser::addressField("direccion", "class: form-control", "placeholder: Direccion"); ?>
                            <label id="direccion-error" class="error" for="direccion"></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="codciu" class="form-control-label">Ciudad notificación:</label>
                            <?= Tag::selectStatic("codciu", $_codciu, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); ?>
                            <label id="codciu-error" class="error" for="codciu"></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="codzon" class="form-control-label">Lugar donde laboran trabajadores:</label>
                            <?= Tag::selectStatic("codzon", $_codzon, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); ?>
                            <label id="codzon-error" class="error" for="codzon"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telefono" class="form-control-label">Telefono notificación con indicativo:</label>
                            <?= Tag::numericField("telefono", "class: form-control", "placeholder: Telefono con Indicativo"); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celular" class="form-control-label">Celular notificación</label>
                            <?= Tag::numericField("celular", "class: form-control", "placeholder: Celular"); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fax" class="form-control-label">Fax notificación</label>
                            <?= Tag::textUpperField("fax", "class: form-control", "placeholder: Fax"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email notificación empresarial</label>
                            <?= Tag::textUpperField("email", "class: form-control", "placeholder: Email"); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="overflow: hidden">
                            <label for="codact" class="form-control-label">Digite el código CIUU-DIAN de la actividad economica:</label>
                            <?= Tag::selectStatic("codact", $_codact, "class: form-control", "select2: true"); ?>
                            <label id="codact-error" class="error" for="codact"></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecini" class="form-control-label">Fecha inicio:</label>
                            <?= TagUser::calendar("fecini", "class: form-control", "placeholder: Fecha Inicial"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tottra" class="form-control-label">Total trabajadores:</label>
                            <?= Tag::textUpperField("tottra", "class: form-control", "placeholder: Total Trabajadores"); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="valnom" class="form-control-label">Valor nomina:</label>
                            <?= Tag::textUpperField("valnom", "class: form-control", "placeholder: Valor Nomina"); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipsoc" class="form-control-label">Tipo sociedad:</label>
                            <?= Tag::selectStatic("tipsoc", $_tipsoc, "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                            <label id="tipsoc-error" class="error" for="tipsoc"></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="codcaj" class="form-control-label">Caja a la que estuvo afiliado antes:</label>
                            <?= Tag::selectStatic("codcaj", $_codcaj, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dirpri" class="form-control-label">Direccion comercial:</label>
                            <?= TagUser::addressField("dirpri", "class: form-control", "placeholder: Direccion Principal"); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ciupri" class="form-control-label">Ciudad comercial:</label>
                            <?= Tag::selectStatic("ciupri", $_ciupri, "class: form-control", "use_dummy: true", "dummyValue: ", "select2: true"); ?>
                            <label id="ciupri-error" class="error" for="ciupri"></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telpri" class="form-control-label">Telefono comercial con indicativo:</label>
                            <?= Tag::numericField("telpri", "class: form-control", "placeholder: Telefono Principal con Indicativo"); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celpri" class="form-control-label">Celular comercial:</label>
                            <?= Tag::numericField("celpri", "class: form-control", "placeholder: Celular Principal"); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="emailpri" class="form-control-label">Email comercial:</label>
                            <?= Tag::textUpperField("emailpri", "class: form-control", "placeholder: Email Principal"); ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-primary" id='guardar_ficha'><i class="fas fa-hand-point-up"></i> Actualizar</button>
                </div>
                <?= Tag::endform(); ?>
            </div>

            <div class="tab-pane fade show" id="archivos_empresa" role="tabpanel" aria-labelledby="archivos_empresa_tab">
                <div class="list-group">
                </div>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    const init_header = function() {
        let _template = _.template($("#tmp_card_header").html());
        let _url = Utils.getKumbiaURL('empresa/index');
        $("#afiliacion_header").html(_template({
            url_salir: _url,
            mostrar_items: "<?= ($datosBasicos == false) ? 'd-none' : '' ?>"
        }));
    };

    $(document).ready(function() {
        init_header();
    });

    function showNameFile(files, _id) {
        if (files.length == 0) return;
        var archivo = files[0];
        $('.toogle-show-name[data-code="' + _id + '"]').html(archivo.name);
    }

    function actualiza_datos_basicos() {
        swal.fire({
            title: "¡Confirmar!",
            html: "<p style='font-size:1rem'>Esta seguro de actualizar los datos?</p>",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-success btn-fill",
            cancelButtonClass: "btn btn-danger btn-fill",
            confirmButtonText: "SI",
            cancelButtonText: "NO",
        }).then(function(result) {
            if (result.value) {
                $("#form").submit();
            }
        });
    }
</script>
@endsection