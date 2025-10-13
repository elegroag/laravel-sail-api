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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])
    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    <script id='tmp_form' type="text/template">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="codapl" class="form-control-label">Aplicativo</label>
                <input type="text" id="codapl" name="codapl" class="form-control" placeholder="Aplicativo">
            </div>
            <div class="form-group">
                <label for="webser" class="form-control-label">WebService</label>
                <input type="text" id="webser" name="webser" class="form-control" placeholder="WebService ">
            </div>
            <div class="form-group">
                <label for="path" class="form-control-label">Path</label>
                <input type="text" id="path" name="path" class="form-control" placeholder="Path">
            </div>
            <div class="form-group">
                <label for="urlonl" class="form-control-label">Url Online</label>
                <input type="text" id="urlonl" name="urlonl" class="form-control" placeholder="Url Online">
            </div>
            <div class="form-group">
                <label for="puncom" class="form-control-label">Puntos por Compartir</label>
                <input type="number" id="puncom" name="puncom" class="form-control" placeholder="Puntos por Compartir">
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio50';
    </script>

    <script src="{{ asset('cajas/build/Mercurio50.js') }}"></script>
@endpush
