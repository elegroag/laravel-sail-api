<?php echo Tag::form("", "id: form_opcion", "class: validation_form", "autocomplete: off", "novalidate"); ?>
<div class="d-flex p-2">
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <?php echo Tag::select("tipopc_08", $mercurio09, "using: tipopc,detalle", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <?php echo Tag::select("usuario_08", $gener02, "using: usuario,nombre", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
        </div>
    </div>
    <div class="form-group">
        <button type="button" id="btnAddOpcion" name="btnAddOpcion" class="btn btn-success" data-toggle='opcion-guardar'>Adicionar</button>
    </div>
</div>
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Opcion</th>
                <th>Usuario</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody id="result_opcion"></tbody>
    </table>
</div>
<?php echo Tag::endform(); ?>