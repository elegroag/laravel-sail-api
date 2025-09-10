<?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
<div class="form-group">
    <label for="codest" class="form-control-label">Codigo</label>
    <?php echo Tag::textUpperField("codest", "class: form-control", "placeholder: Codigo"); ?>
</div>
<div class="form-group">
    <label for="detalle" class="form-control-label">Detalle</label>
    <?php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); ?>
</div>
<?php echo Tag::endform(); ?>