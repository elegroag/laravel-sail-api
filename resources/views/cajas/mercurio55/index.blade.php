<?php
echo View::getContent();
Tag::addJavascript('core/global');
Tag::addJavascript('Cajas/movile/mercurio55');
echo Tag::help($title, $help);
echo Tag::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
<div class="modal fade" id="capture-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0">
                    <div class="card-header bg-secondary">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h3 class="mb-0"><?php echo $title; ?></h3>
                            </div>
                            <div class="col-2 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
                        <div class="form-group">
                            <label for="codare" class="form-control-label">Area</label>
                            <?php echo Tag::textUpperField("codare", "class: form-control", "placeholder: Area"); ?>
                        </div>
                        <div class="form-group">
                            <label for="detalle" class="form-control-label">Detalle</label>
                            <?php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); ?>
                        </div>
                        <div class="form-group">
                            <label for="codcat" class="form-control-label">Categoria</label>
                            <?php echo Tag::select("codcat", $Mercurio51->find(), "using: codcat,detalle", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
                        </div>
                        <div class="form-group">
                            <label for="tipo" class="form-control-label">Tipo</label>
                            <?php echo Tag::selectStatic("tipo", $Mercurio51->getTipoArray(), "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
                        </div>
                        <div class="form-group">
                            <label for="estado" class="form-control-label">Estado</label>
                            <?php echo Tag::selectStatic("estado", $Mercurio51->getEstadoArray(), "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
                        </div>
                        <?php echo Tag::endform(); ?>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary" onclick="guardar();">Guardar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
