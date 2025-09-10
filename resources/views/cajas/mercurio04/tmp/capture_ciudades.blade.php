 <?php echo Tag::form("", "id: form_ciudad", "class: validation_form", "autocomplete: off", "novalidate"); ?>
 <div class="d-flex p-2">
     <div class="col-sm-9 col-md-6">
         <div class="form-group">
             <?php echo Tag::selectStatic("codciu_05", $ciudades, "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
         </div>
     </div>
     <div class="form-group">
         <button type="button" id="btnAddCiudad" name="btnAddCiudad" class="btn btn-success" data-toggle='ciudad-guardar'>Adicionar</button>
     </div>
 </div>
 <div>
     <table class="table table-striped">
         <thead>
             <tr>
                 <th>Ciudad</th>
                 <th>&nbsp;</th>
             </tr>
         </thead>
         <tbody id="result_ciudad"></tbody>
     </table>
 </div>
 <?php echo Tag::endform(); ?>