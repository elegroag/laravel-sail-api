<?php
echo View::getContent();
echo TagUser::help($title, $help);
?>

<div class="pb-3">
    <div class="card-body">
        <?php echo Tag::form("subsidioemp/certificado_afiliacion", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <p class="text-center">Genera el certificado de afiliación</p>
            <div class="col-md-auto d-flex m-auto">
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