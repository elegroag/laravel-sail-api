<div class="modal fade" id="{{ $idModal }}" aria-hidden="true" aria-labelledby="{{ isset($btnShowModal) ? $btnShowModal : 'btn-modal' }}"  role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card mb-0">
                    @if (!isset($hideHeader) || $hideHeader == false)
                    <div class="card-header bg-primary">
                        <div class="row align-items-center">
                            <div class="col-10">
                                @if (isset($titulo))
                                    <h5 class="mb-0 text-white">{{ $titulo }}</h5>
                                @endif
                            </div>
                            <div class="col-2 text-right">
                                <button 
                                    type="button" 
                                    class="btn-close" 
                                    data-bs-dismiss="modal" 
                                    aria-label="Close" 
                                    data-bs-target="#{{ $idModal }}"></button>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="card-body" id='{{ $idModal }}body'>
                        @if (isset($contenido))
                            @php echo $contenido @endphp
                        @endif
                    </div>
                    @if (!isset($hideFooter) || $hideFooter == false)
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary" @php echo $evento @endphp>Guardar</button>
                        <button type="button" 
                            class="btn btn-danger" 
                            data-bs-dismiss="modal" 
                            data-bs-target="#{{ $idModal }}" 
                            aria-label="Close">Cerrar
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@if (isset($btnShowModal))
<a class="d-none" data-bs-toggle="modal" id="{{ $btnShowModal }}" href="#{{ $idModal }}" role="button"></a>
@endif