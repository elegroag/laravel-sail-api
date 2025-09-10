<?php
echo View::getContent();
Tag::addJavascript('core/global');
echo TagUser::help($title, $help);
echo Tag::addJavascript('Cajas/consulta');
?>

<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("reportes/reporte_auditoria", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="fecini" class="form-control-label">Fecha Inicial</label>
                    <?php echo TagUser::calendar("fecini", "placeholder: Fecha Inicial", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                    <?php echo TagUser::calendar("fecfin", "placeholder: Fecha Final", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-primary align-self-center" onclick="consulta_indicadores();">Consultar</button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<div id='consulta' class='table-responsive'>
</div>