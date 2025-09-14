@php
use App\Services\Tag;
@endphp

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
                <h3 class="mb-0">@php echo $title; @endphp</h3>
              </div>
              <div class="col-2 text-right">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            @php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
            <div class="form-group">
              <label for="codapl" class="form-control-label">Aplicativo</label>
              @php echo Tag::textUpperField("codapl", "class: form-control", "placeholder: Aplicativo"); @endphp
            </div>
            <div class="form-group">
              <label for="webser" class="form-control-label">WebService</label>
              @php echo Tag::textField("webser", "class: form-control", "placeholder: WebService "); @endphp
            </div>
            <div class="form-group">
              <label for="path" class="form-control-label">Path</label>
              @php echo Tag::textField("path", "class: form-control", "placeholder: Path"); @endphp
            </div>
            <div class="form-group">
              <label for="urlonl" class="form-control-label">Url Online</label>
              @php echo Tag::textField("urlonl", "class: form-control", "placeholder: Url Online"); @endphp
            </div>
            <div class="form-group">
              <label for="urlonl" class="form-control-label">Puntos por Compartir</label>
              @php echo Tag::numericField("puncom", "class: form-control", "placeholder: Puntos por Compartir"); @endphp
            </div>
            @php echo Tag::endform(); @endphp
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

<script src="{{ asset('core/global.js') }}"></script>
<script src="{{ asset('Cajas/movile/mercurio50.js') }}"></script>
