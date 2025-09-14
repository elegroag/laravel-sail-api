<div
    class="modal fade"
    id="{{ $__idModal }}"
    aria-hidden="true"
    aria-labelledby="{{ $__btnShowModal }}"
    tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0">
                    <div class="card-header bg-primary">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <h5 class="mb-0 text-white">{{ $titulo }}</h5>
                            </div>
                            <div class="col-2 text-right">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#{{ $__idModal }}"></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ $contenido }}
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary" {!! $evento !!}>Guardar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-target="#{{ $__idModal }}" aria-label="Close">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a class="d-none" data-bs-toggle="modal" id='{{ $__btnShowModal }}' href="#{{ $__idModal }}" role="button">Crear</a>