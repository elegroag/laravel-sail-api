<?php
echo View::getContent();
echo TagUser::help($title, $help);
echo Tag::addJavascript('Mercurio/core/upload');
?>

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-6 ml-auto">
                <div class="form-group">
                    <label for="cedtra" class="form-control-label">Cedula</label>
                    <?php echo Tag::numericField("cedtra", "placeholder: Cedula", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre" class="form-control-label">Nombre</label>
                    <?php echo Tag::numericField("nombre", "placeholder: Nombre", "disabled: true", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex m-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" onclick="buscar_trabajador();">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Buscar Trabajador</span>
                </button>
                <button type="button" class="btn btn-icon btn-warning align-self-center" onclick="window.location.reload();">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Reiniciar</span>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="codest" class="form-control-label">Motivo</label>
                    <?php echo Tag::selectStatic("codest", $codest, "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
                    <?php echo Tag::hiddenField("fecafi"); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecret" class="form-control-label">Fecha Retiro</label>
                    <?php echo TagUser::calendar("fecret", "placeholder: Fecha Retiro", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="archivo" class="form-control-label">Archivo</label>
                    <?php echo Tag::fileField("archivo", "placeholder: Nombre", "class: form-control", "accept: application/pdf, image/*"); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="nota" class="form-control-label">Nota</label>
                    <?php echo Tag::textArea("nota", "rows: 5", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex m-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_novedad_retiro">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Retirar Trabajador</span>
                </button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<?= Tag::javascriptInclude('Mercurio/consultasempresa/consultasempresa.build'); ?>