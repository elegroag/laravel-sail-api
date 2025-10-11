@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => true])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-3">
                    <form id="form" class="validation_form" autocomplete="off" novalidate>
                        <div class="row">
                            <div class="col-md-6 ml-auto">
                                <div class="form-group">
                                    <label for="archivo" class="form-control-label">Archivo</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="archivo" name="archivo" lang="es">
                                        <label class="custom-file-label" for="archivo">Seleccione un archivo</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo" class="form-control-label">Tipo</label>
                                    <select id="tipo" name="tipo" class="form-control">
                                        <option value="">Seleccione</option>
                                        <option value="F">FOTO</option>
                                        <option value="V">VIDEO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mr-auto">
                                <button type="button" class="btn btn-primary" style="margin-top: 17%" data-toggle="guardar">Agregar</button>
                            </div>
                        </div>
                    </form>

                    <div class="row border-top d-flex flex-wrap mt-2 pt-3" id="galeria"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include("partials.modal_generic", [
        "titulo" => 'Imagen Zoom',
        "contenido" => '',
        "evento" => 'data-toggle="show-modal"',
        "btnShowModal" => 'btZoomModal',
        "idModal" => 'zoomModal']
    )

    <script type="text/template" id='tmp_galeria'>
        <div class="col-lg-3 col-md-4 col-xs-6 mb-3">
            <button
                type="button"
                style="float: right; z-index:9999"
                class="btn btn-default btn-sm btn-icon-only rounded-circle mt-2"
                data-toggle="borrar"
                data-cid="<%=value.numero %>">
                    <i class="fa fa-times"></i>
            </button>

            <% if (value.tipo == 'V') { %>
            <div class="thumbnail" style="position: absolute; width:100%">
                <video width="90%" height="240" controls> <source src="<%=value.archivo%>" type="video/mp4"></video>
            <% } else { %>
            <div class="thumbnail"
                style="opacity:1;background-image: url('<%=value.archivo%>');background-size: 100% 100%;border-top: solid 1px #e5e5e5;border-right: solid 2px #e5e5e5;border-bottom: solid 2px #e5e5e5;border-left: solid 1px #e5e5e5;border-color: #e5e5e5;cursor: zoom-in;"
                data-toggle="show-modal"
                data-cid='<%=value.numero%>'
                data-file='<%=value.archivo%>'
                >
            <% } %>
                <div class="caption" style="background: rgba(108, 108, 108, 0.6); margin-top: 65%; text-align: center;">
                    <h4 class="text-white">Imagen N° <%= value.numero%> </h4>
                    <div class="pb-2">
                        <button type="button" class="btn btn-icon-only btn-info" data-toggle="arriba" data-cid="<%=value.numero%>">
                            <i class="fas fa-long-arrow-alt-left"></i>
                        </button>
                        <button
                            type="button" class="btn btn-icon-only btn-info" data-toggle="abajo"
                            data-cid="<%=value.numero%>">
                                <i class="fas fa-long-arrow-alt-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script>
        window.ServerController = 'mercurio26';
    </script>

    <script src="{{ asset('cajas/build/Galeria.js') }}"></script>
@endpush
