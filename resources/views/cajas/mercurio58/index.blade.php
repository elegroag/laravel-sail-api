@php
use App\Services\Tag;

echo Tag::help($title, $help);
@endphp

<div class="card-body border-top">
    @php echo Tag::form("id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
    <div class="row">
        <div class="col-md-6 ml-auto">
            <div class="form-group">
                <div class="custom-file">
                    <input type="hidden" class="custom-file-input" id="codare" name="codare" value="{{ $codare }}" lang="es">
                    <input type="file" class="custom-file-input" id="archivo" name="archivo" lang="es">
                    <label class="custom-file-label" for="customFileLang">Seleccione un archivo</label>
                </div>
            </div>
        </div>
        <div class="col-md-auto mr-auto">
            <button type="button" class="btn btn-primary " onclick="guardar();">Agregar</button>
        </div>
    </div>
    @php echo Tag::endform(); @endphp

    <div class="row border-top d-flex flex-wrap mt-2 pt-3" id="galeria">
    </div>

</div>


<div id="modal_imagen" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" style="position:absolute;top:7px;right:5px;z-index:100;" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <img id="img_zoom" class="img-fluid" src="" />
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('core/global.js') }}"></script>
<script src="{{ asset('Cajas/movile/mercurio58.js') }}"></script>
