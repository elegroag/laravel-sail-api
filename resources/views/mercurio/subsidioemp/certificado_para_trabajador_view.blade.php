<?php
echo View::getContent();
echo TagUser::help($title, $help);
?>

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("subsidioemp/certificado_para_trabajador", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="cedtra" class="form-control-label">Trabajador</label>
                    <?php echo Tag::selectStatic("cedtra", $_cedtra, "use_dummy: true", "dummyValue: ", "class: form-control", "select2: true"); ?>
                </div>
            </div>
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="tipo" class="form-control-label">Tipo</label>
                    <?php echo Tag::selectStatic("tipo", $tipo, "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_certificado_afiliacion">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Generar Certificado</span>
                </button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<?= Tag::javascriptInclude('Mercurio/consultasempresa/consultasempresa.build'); ?>