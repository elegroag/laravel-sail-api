 <?php echo Tag::form("", "id: form_campo", "class: validation_form", "autocomplete: off", "novalidate"); ?>
 <div class="d-flex p-2">
     <div class="col-sm-6 col-md-3">
         <div class="form-group">
             <?php echo Tag::textField("campo_28", "class: form-control"); ?>
         </div>
     </div>
     <div class="col-sm-6 col-md-3">
         <div class="form-group">
             <?php echo Tag::textUpperField("detalle_28", "class: form-control"); ?>
         </div>
     </div>
     <div class="col-sm-6 col-md-3">
         <div class="form-group">
             <?php echo Tag::textUpperField("orden_28", "class: form-control"); ?>
         </div>
     </div>
     <div class="form-group">
         <button type="button" id="btnAdicionar" name="btnAdicionar" class="btn btn-success" data-toggle='campo-guardar'>Adicionar</button>
     </div>
 </div>
 <div>
     <table class="table table-striped">
         <thead>
             <tr>
                 <th>Campo</th>
                 <th>Detalle</th>
                 <th>Orden</th>
                 <th>&nbsp;</th>
             </tr>
         </thead>
         <tbody id="result_campos">
         </tbody>
     </table>
 </div>
 <?php echo Tag::endform(); ?>