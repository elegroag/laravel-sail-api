 <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
 <div class="form-group">
     <label for="codfir" class="form-control-label">Firma</label>
     <?php echo Tag::textUpperField("codfir", "class: form-control", "placeholder: Firma"); ?>
 </div>
 <div class="form-group">
     <label for="nombre" class="form-control-label">Nombre</label>
     <?php echo Tag::textUpperField("nombre", "class: form-control", "placeholder: Nombre"); ?>
 </div>
 <div class="form-group">
     <label for="cargo" class="form-control-label">Cargo</label>
     <?php echo Tag::textUpperField("cargo", "class: form-control", "placeholder: Cargo"); ?>
 </div>
 <div class="form-group">
     <label for="archivo" class="form-control-label">Archivo</label>
     <div class='custom-file'>
         <input type='file' class='custom-file-input' id='archivo' name='archivo'>
         <label class='custom-file-label' for='customFileLang'>Select file</label>
     </div>
 </div>
 <div class="form-group">
     <label for="email" class="form-control-label">Email</label>
     <?php echo Tag::textUpperField("email", "class: form-control", "placeholder: Email"); ?>
 </div>
 <?php echo Tag::endform(); ?>