<?php
echo View::getContent();
Tag::addJavascript('core/global');
echo Tag::addJavascript('Cajas/consulta');
echo TagUser::help($title, $help);
?>
<div class="card mb-0">
    <div class="card-body">
        <?php echo Tag::form("consultafoninez/reporte_jec", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="row">
            <div class="col-md-2 ml-auto">
                <div class="form-group">
                    <label for="fecini" class="form-control-label">Fecha Inicial</label>
                    <?php echo TagUser::calendar("fecini", "placeholder: Fecha Inicial", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                    <?php echo TagUser::calendar("fecfin", "placeholder: Fecha Final", "class: form-control"); ?>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-danger align-self-center" onclick="reporte_jec();">Reporte</button>
            </div>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>
<div id='consulta' class='table-responsive'></div>

<!-- Modal Captura -->
<div class="modal fade" id="capture-modal-info" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0">
                    <div class="card-header bg-secondary">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h3 class="mb-0"><?php echo "Información"; ?></h3>
                            </div>
                            <div class="col-2 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="result_info">
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>