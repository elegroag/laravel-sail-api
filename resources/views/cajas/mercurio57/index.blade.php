@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <style>
       .galerya-item { 
            opacity: 1;
            background-size: 100% 100%; 
            border-top: solid 1px #e5e5e5;
            border-right: solid 2px #e5e5e5;
            border-bottom: solid 2px #e5e5e5;
            border-left: solid 1px #e5e5e5;
            border-color: #e5e5e5;
            cursor: zoom-in;
       }
    </style> 
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
</div>
@endsection

@push('scripts')
    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    @include("partials.modal_generic", [
        "titulo" => 'Imagen',
        "contenido" => '',
        "evento" => 'data-toggle="imagen-guardar"',
        "btnShowModal" => 'btImagenModal',
        "idModal" => 'modalImagen']
    )

    <script id='tmp_form' type="text/template">
        <form id="form" method="#" class="validation_form" autocomplete="off" novalidate>
            <div class="row justify-content-around">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="archivo" class="form-control-label">Archivo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="archivo" name="archivo" lang="es">
                            <label class="custom-file-label" for="customFileLang">Seleccione un archivo</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="url" class="form-control-label">Url</label>
                        <input type="text" name="url" id="url" class="form-control" placeholder="Url">
                    </div>
                </div>
            </div>
        </form>
    </script>

    <script type='text/template' id='tmp_galeria_item'>
        <% 
        _.each(_collection, function(value, key) { %>
        <div class="col-lg-3 col-md-4 col-xs-6 mb-3">
			<div class="thumbnail galeria-item" 
                style="background-image: url(<%=value.archivo%>);" 
                data-toggle="modal" 
                data-target="#modal_imagen">
			
                <button type="button" 
                    style="float: right;" 
                    class="btn btn-default btn-sm btn-icon-only rounded-circle mt-2" 
                    data-cid='<%=value.numpro%>'
                    data-toggle="borrar">
                        <i class="fa fa-times"></i>
                </button>
                <div class="caption" style="background: rgba(108, 108, 108, 0.6); margin-top: 65%; text-align: center;">
                    <h4 class="text-white">Imagen N°<%=value.numpro%></h4>
                    <p class="pb-2">
                        <button type="button" class="btn btn-icon-only btn-info" 
                            data-cid='<%=value.numpro%>'
                            data-toggle="arriba">
                                <i class="fas fa-long-arrow-alt-left"></i>
                        </button>
                        <button type="button" class="btn btn-icon-only btn-info" 
                            data-cid='<%=value.numpro%>'
                            data-toggle="abajo">
                                <i class="fas fa-long-arrow-alt-right"></i>
                        </button>
                    </p>
                </div>
			</div>
        </div>
        <% }) %>
    </script>

    <script type='text/template' id='tmp_modal_imagen'>
        <button 
            type="button" 
            style="position:absolute;top:7px;right:5px;z-index:100;" 
            class="close" 
            data-dismiss="modal">
                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <img id="img_zoom" class="img-fluid" src="" />
    </script>

    <script>
        window.ServerController = 'mercurio57';
    </script>

    <script src="{{ asset('cajas/build/Mercurio57.js') }}"></script>
@endpush
