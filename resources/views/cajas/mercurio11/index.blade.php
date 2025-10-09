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
        <form id="form" method="#" class="validation_form" autocomplete="off" novalidate >
            <div class="row justify-content-between">
                <div class="form-group col-4">
                    <label for="codest" class="form-control-label">Codigo</label>
                    <input type="text" id="codest" name="codest" class="form-control" placeholder="Codigo" required value="<%= codest %>">
                </div>
                <div class="form-group col-8">
                    <label for="detalle" class="form-control-label">Detalle</label>
                    <input type="text" id="detalle" name="detalle" class="form-control" placeholder="Detalle" required value="<%= detalle %>">
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio11';
    </script>

    <script src="{{ asset('cajas/build/MotivoRechazo.js') }}"></script>
@endpush    