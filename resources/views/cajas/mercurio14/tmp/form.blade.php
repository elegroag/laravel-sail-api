<form id="form" method='POST'>
    <div class="row justify-content-start">
        <div class="form-group mb-2" style="width: 50%;">
            <label for="tipopc" class="form-control-label">Tipo afiliación</label>
            <?= Tag::selectStatic("tipopc", $tipopc, "class: form-control", "placeholder: Tipo PC"); ?>
        </div>
        <div class="form-group mb-2" style="width: 50%;">
            <label for="coddoc" class="form-control-label">Documento</label>
            <?= Tag::selectStatic("coddoc", $coddoc, "class: form-control", "placeholder: Documento"); ?>
        </div>
        <div class="form-group mb-2" style="width: 50%;">
            <label for="tipsoc" class="form-control-label">Tipo sociedad</label>
            <?= Tag::selectStatic("tipsoc", $tipsoc, "class: form-control", "placeholder: Código Doc"); ?>
        </div>
        <div class="form-group mb-2" style="width: 140px;">
            <label for="obliga" class="form-control-label">Obligatorio</label>
            <?= Tag::selectStatic("obliga", array('N' => 'NO', 'S' => 'SI'), "class: form-control", "placeholder: obligatorio"); ?>
        </div>
        <div class="form-group mb-2" style="width: 140px;">
            <label for="auto_generado" class="form-control-label">Auto generado</label>
            <?= Tag::selectStatic("auto_generado", array('0' => 'NO', '1' => 'SI'), "class: form-control", "placeholder: Auto Generado"); ?>
        </div>
        <div class="form-group mb-2" style="width: 100%;">
            <label for="nota" class="form-control-label">Nota observaciones:</label>
            <textarea id="nota" name='nota' class="form-control" placeholder="NOTA" rows="3""></textarea>
        </div>
    </div>
</form>