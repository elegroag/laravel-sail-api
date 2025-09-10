 <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
 <div class="form-group">
     <label for="tipopc" class="form-control-label">Tipo</label>
     <?php echo Tag::textUpperField("tipopc", "class: form-control", "placeholder: Tipo"); ?>
 </div>
 <div class="form-group">
     <label for="detalle" class="form-control-label">Detalle</label>
     <?php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); ?>
 </div>
 <div class="form-group">
     <label for="dias" class="form-control-label">Dias</label>
     <?php echo Tag::numericField("dias", "class: form-control", "placeholder: Dias"); ?>
 </div>
 <?php echo Tag::endform(); ?>