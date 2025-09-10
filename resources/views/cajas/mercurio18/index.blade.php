<?php
echo View::getContent();
echo Tag::addJavascript('Cajas/global');
echo Tag::addJavascript('Cajas/mercurio18');
echo TagUser::help($title, $help);
echo TagUser::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
<div class="modal fade" id="capture-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card mb-0">
          <div class="card-header bg-secondary">
            <div class="row align-items-center">
              <div class="col-10">
                <h3 class="mb-0"><?php echo $title; ?></h3>
              </div>
              <div class="col-2 text-right">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); ?>
            <div class="form-group">
              <label for="codigo" class="form-control-label">Codigo</label>
              <?php echo Tag::textUpperField("codigo", "class: form-control", "placeholder: Codigo"); ?>
            </div>
            <div class="form-group">
              <label for="detalle" class="form-control-label">Detalle</label>
              <?php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); ?>
            </div>
            <?php echo Tag::endform(); ?>
          </div>
          <div class="card-footer text-right">
            <button type="button" class="btn btn-primary" onclick="guardar();">Guardar</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>