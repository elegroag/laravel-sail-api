@php
use App\Services\Tag;

// Scripts se moverán al final
echo Tag::filtro($campo_filtro);
@endphp

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
<div class="modal fade" id="capture-modal" role="dialog" aria-hidden="true">
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
                            <label for="codser" class="form-control-label">Servicio</label>
                            @php echo Tag::hiddenField("codinf", "value: {$codinf}"); @endphp
                            @php echo Tag::selectStatic("codser", $_codser, "use_dummy: true", "select2: true", "dummyValue: ", "class: form-control"); @endphp
                        </div>
                        <div class="form-group">
                            <label for="numero" class="form-control-label">Numero</label>
                            <span id='td_apertura'>
                                @php echo Tag::selectStatic("numero", array(), "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            @php echo Tag::textUpperField("email", "class: form-control", "placeholder: Email"); @endphp
                        </div>
                        <div class="form-group">
                            <label for="nota" class="form-control-label">Nota</label>
                            @php echo Tag::textUpperField("nota", "class: form-control", "placeholder: Nota"); @endphp
                        </div>
                        <div class="form-group">
                            <label for="precan" class="form-control-label">Pregunta Cantidad?</label>
                            @php echo Tag::selectStatic("precan", $Mercurio59->getPrecanArray(), "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                        </div>
                        <div class="form-group">
                            <label for="autser" class="form-control-label">Automatico Servicio?</label>
                            @php echo Tag::selectStatic("autser", $Mercurio59->getAutserArray(), "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                        </div>
                        <div class="form-group">
                            <label for="consumo" class="form-control-label">Valida Consumo?</label>
                            @php echo Tag::selectStatic("consumo", $Mercurio59->getConsumoArray(), "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                        </div>
                        <div class="form-group">
                            <label for="estado" class="form-control-label">Estado</label>
                            @php echo Tag::selectStatic("estado", $Mercurio59->getEstadoArray(), "class: form-control", "use_dummy: true", "dummyValue: "); @endphp
                        </div>
                        <div class="form-group">
                            <label for="archivo" class="form-control-label">Archivo</label>
                            <div class='custom-file'>
                                <input type='file' class='custom-file-input' id='archivo' name='archivo'>
                                <label class='custom-file-label' for='customFileLang'>Select file</label>
                            </div>
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
<script src="{{ asset('Cajas/movile/mercurio59.js') }}"></script>
<script src="{{ asset('Cajas/movile/upload.js') }}"></script>
