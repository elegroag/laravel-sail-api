<?php

?>

<div class="row">
    <div class="col-3"> </div>
    <div class="col-6">
        <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
        <div class="form-group">
            <label for="tipfun" class="form-control-label">Usuario</label>
            <?php echo Tag::select("usuario", $Gener02->find(), "using: usuario,nombre", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
        </div>
        <?php echo Tag::endform(); ?>
    </div>
</div>

<div class="row card-body">
    <div id='nopermite' class='card col-5' style='overflow: auto; height: 500px;'> </div>
    <div class='col-2' style='align-self: center;'>
        <button type="button" class="btn btn-primary btn-lg" toggle-event='agregar' style='width: 100%;'>
            Agregar
            <i class="fas fa-arrow-right"></i>
        </button> <br><br>
        <button type="button" class="btn btn-primary btn-lg" toggle-event='quitar' style='width: 100%;'>
            <i class="fas fa-arrow-left"></i>
            Quitar
        </button>
    </div>
    <div id='permite' class='card col-5' style='overflow: auto; height: 500px;'> </div>
</div>


<?= Tag::javascriptInclude('Cajas/permisos/build.permisos'); ?>
