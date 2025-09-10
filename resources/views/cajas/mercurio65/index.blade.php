<?php
echo View::getContent();
Tag::addJavascript('core/global');
Tag::addJavascript('Cajas/movile/mercurio65');
Tag::addJavascript('Cajas/movile/upload');

echo TagUser::help($title, $help);
echo TagUser::filtro($campo_filtro);
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
                            <label for="codsed" class="form-control-label">Codigo</label>
                            <?php echo Tag::hiddenField("codsed", "class: form-control"); ?>
                            <?php echo Tag::numericField("nit", "class: form-control", "placeholder: Nit"); ?>
                        </div>
                        <div class="form-group">
                            <label for="razsoc" class="form-control-label">Razon Social</label>
                            <?php echo Tag::textUpperField("razsoc", "class: form-control", "placeholder: Razon social"); ?>
                        </div>
                        <div class="form-group">
                            <label for="direccion" class="form-control-label">Direccion</label>
                            <?php echo Tag::textUpperField("direccion", "class: form-control", "placeholder: Direccion"); ?>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <?php echo Tag::textUpperField("email", "class: form-control", "placeholder: Email"); ?>
                        </div>
                        <div class="form-group">
                            <label for="celular" class="form-control-label">Celular</label>
                            <?php echo Tag::numericField("celular", "class: form-control", "placeholder: Celular"); ?>
                        </div>
                        <div class="form-group">
                            <label for="codcla" class="form-control-label">Clasificacion</label>
                            <?php echo Tag::select("codcla", $Mercurio67->find(), "using: codcla,detalle", "class: form-control", "use_dummy: true", "dummyValue: "); ?>
                        </div>
                        <div class="form-group">
                            <label for="detalle" class="form-control-label">Detalle</label>
                            <?php echo Tag::numericField("detalle", "class: form-control", "placeholder: Detalle"); ?>
                        </div>
                        <div class="form-group">
                            <label for="lat" class="form-control-label">Latitud</label>
                            <?php echo Tag::textField("lat", "class: form-control", "placeholder: Latitud"); ?>
                        </div>
                        <div class="form-group">
                            <label for="log" class="form-control-label">Longitud</label>
                            <?php echo Tag::textField("log", "class: form-control", "placeholder: Longitud"); ?>
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
                            <?php echo Tag::selectStatic("estado", $Mercurio65->getEstadoArray(), "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
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