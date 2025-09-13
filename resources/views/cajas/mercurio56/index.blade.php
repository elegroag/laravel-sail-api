<?php
echo View::getContent();
Tag::addJavascript('core/global');
Tag::addJavascript('Cajas/movile/mercurio56');
Tag::addJavascript('Cajas/movile/upload');

echo Tag::help($title, $help);
echo Tag::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
<div class="modal fade" id="capture-modal" role="dialog" aria-hidden="true">
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
                            <label for="codinf" class="form-control-label">Codigo</label>
                            <?php echo Tag::selectStatic("codinf", $_infraestructura, "use_dummy: true", "select2: true", "dummyValue: ", "class: form-control"); ?>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <?php echo Tag::textUpperField("email", "class: form-control", "placeholder: Email"); ?>
                        </div>
                        <div class="form-group">
                            <label for="telefono" class="form-control-label">Telefono</label>
                            <?php echo Tag::numericField("telefono", "class: form-control", "placeholder: Telefono"); ?>
                        </div>
                        <div class="form-group">
                            <label for="nota" class="form-control-label">Nota</label>
                            <?php echo Tag::textUpperField("nota", "class: form-control", "placeholder: Nota"); ?>
                        </div>
                        <div class="form-group">
                            <label for="archivo" class="form-control-label">Archivo</label>
                            <div class='custom-file'>
                                <input type='file' class='custom-file-input' id='archivo' name='archivo'>
                                <label class='custom-file-label' for='customFileLang'>Select file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="estado" class="form-control-label">Estado</label>
                            <?php echo Tag::selectStatic("estado", $Mercurio56->getEstadoArray(), "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
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
