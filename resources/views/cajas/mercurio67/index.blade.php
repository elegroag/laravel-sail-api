@php
use App\Services\Tag;

echo Tag::filtro($campo_filtro);
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
                                <h3 class="mb-0">{{ $title }}</h3>
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
                            <label for="codcla" class="form-control-label">Codigo</label>
                            @php echo Tag::textUpperField("codcla", "class: form-control", "placeholder: Codigo"); @endphp
                        </div>
                        <div class="form-group">
                            <label for="detalle" class="form-control-label">Detalle</label>
                            @php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); @endphp
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
<script src="{{ asset('Cajas/movile/mercurio67.js') }}"></script>
