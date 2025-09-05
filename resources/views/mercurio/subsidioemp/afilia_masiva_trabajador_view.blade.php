<?php
echo View::getContent();
echo TagUser::help($title, $help);
echo Tag::addJavascript('Mercurio/subsidioemp');
echo Tag::addJavascript('Mercurio/core/upload');
?>

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("subsidioemp/afilia_masiva_trabajador", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-5 ml-auto">
                <div class="form-group">
                    <label for="archivo" class="form-control-label">Archivo</label>
                    <?php echo Tag::fileField("archivo", "class: form-control", "accept: .csv,text/plain"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_afilia_masiva_trabajador">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Cargue Masivo</span>
                </button>
                <div class="col-md-auto d-flex mr-auto">
                    <button type="button" class="btn btn-danger align-self-center" id="bt_ejemplo_planilla_masiva">Ejemplo</button>
                </div>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>


<div id='consulta' class='table-responsive'>
</div>

<?= Tag::javascriptInclude('Mercurio/consultasempresa/consultasempresa.build'); ?>