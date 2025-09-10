<?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
<div class="form-group">
    <label for="codofi" class="form-control-label">Oficina</label>
    <?php echo Tag::textUpperField("codofi", "class: form-control", "placeholder: Oficina"); ?>
</div>
<div class="form-group">
    <label for="detalle" class="form-control-label">Detalle</label>
    <?php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); ?>
</div>
<div class="form-group">
    <label for="principal" class="form-control-label">Principal</label>
    <?php echo Tag::selectStatic("principal", $principal, "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
</div>
<div class="form-group">
    <label for="estado" class="form-control-label">Estado</label>
    <?php echo Tag::selectStatic("estado", $estados, "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
</div>
<?php echo Tag::endform(); ?>