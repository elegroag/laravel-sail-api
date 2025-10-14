@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div id='consulta' class='table-responsive'></div>
                    <div id='paginate' class='card-footer py-4'></div>
                    <div class="row border-top d-flex flex-wrap mt-2 pt-3" id="galeria"></div>
                </div>
            </div>
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
</div>
@endsection

@push('scripts')
    @include("partials.modal_generic", [
        "titulo" => 'Carga de archivo',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    <script id='tmp_form' type="text/template">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-md-6 ml-auto">
                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="archivo" name="archivo" lang="es">
                            <label class="custom-file-label" for="customFileLang">Seleccione un archivo</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-auto mr-auto">
                    <button type="button" class="btn btn-primary " onclick="guardar();">Agregar</button>
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio53';
    </script>

    <script src="{{ asset('cajas/build/Mercurio53.js') }}"></script>
@endpush
