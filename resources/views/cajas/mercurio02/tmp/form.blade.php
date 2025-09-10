<?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
<div class="form-group">
    <label for="codcaj" class="form-control-label">Codigo Caja</label>
    <?php echo Tag::textUpperField("codcaj", "class: form-control", "placeholder: Caja"); ?>
</div>
<div class="form-group">
    <label for="nit" class="form-control-label">Nit</label>
    <?php echo Tag::textUpperField("nit", "class: form-control", "placeholder: Nit "); ?>
</div>
<div class="form-group">
    <label for="razsoc" class="form-control-label">Razon Social</label>
    <?php echo Tag::textUpperField("razsoc", "class: form-control", "placeholder: Razon Social"); ?>
</div>
<div class="form-group">
    <label for="sigla" class="form-control-label">Sigla</label>
    <?php echo Tag::textUpperField("sigla", "class: form-control", "placeholder: Sigla"); ?>
</div>
<div class="form-group">
    <label for="email" class="form-control-label">Email</label>
    <?php echo Tag::textField("email", "class: form-control", "placeholder: Email"); ?>
</div>
<div class="form-group">
    <label for="direccion" class="form-control-label">Direccion</label>
    <?php echo Tag::textUpperField("direccion", "class: form-control", "placeholder: Direccion"); ?>
</div>
<div class="form-group">
    <label for="telefono" class="form-control-label">Telefono</label>
    <?php echo Tag::numericField("telefono", "class: form-control", "placeholder: Telefono"); ?>
</div>
<div class="form-group">
    <label for="codciu" class="form-control-label">Ciudad</label>
    <?php echo Tag::selectStatic("codciu", $ciudades, "use_dummy: true", "dummyValue: ", "class: form-control", "select2: true"); ?>
</div>
<div class="form-group">
    <label for="pagweb" class="form-control-label">Pagina Web</label>
    <?php echo Tag::textField("pagweb", "class: form-control", "placeholder: Pagina Web"); ?>
</div>
<div class="form-group">
    <label for="pagfac" class="form-control-label">Pagina Facebook</label>
    <?php echo Tag::textField("pagfac", "class: form-control", "placeholder: Pagina Facebook"); ?>
</div>
<div class="form-group">
    <label for="pagtwi" class="form-control-label">Pagina Twiter</label>
    <?php echo Tag::textField("pagtwi", "class: form-control", "placeholder: Pagina Twiter"); ?>
</div>
<div class="form-group">
    <label for="pagyou" class="form-control-label">Pagina Youtube</label>
    <?php echo Tag::textField("pagyou", "class: form-control", "placeholder: Pagina Youtube"); ?>
</div>
<?php echo Tag::endform(); ?>