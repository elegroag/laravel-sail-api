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

    @include("partials.modal_generic", [
        "titulo" => 'Archivos trabajadores',
        "contenido" => '',
        "evento" => 'data-toggle="guardar-archivo"',
        "btnShowModal" => 'btCaptureArchivos',
        "idModal" => 'capturaArchivos']
    )

    @include("partials.modal_generic", [
        "titulo" => 'Archivos empresas',
        "contenido" => '',
        "evento" => 'data-toggle="guardar-archivo"',
        "btnShowModal" => 'btCaptureEmpresa',
        "idModal" => 'capturaEmpresa']
    )

    <script type="text/template" id="tmp_capture_empresa">
        <div class="container-fluid">
            <div class="col p-3">
                <label for="tipsoc" class="form-control-label">Tipo</label>
                <select id="tipsoc" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($_tipsoc as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row" id='div_archivos_empresa'></div>
        </div>
    </script>

    <script type="text/template" id="tmp_capture_archivos">
        <div class="container-fluid">
            <div class="row" id='div_archivos'></div>
        </div>
    </script>

    <script type="text/template" id="tmp_form">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="tipopc" class="form-control-label">Tipo</label>
                <input type="text" id="tipopc" class="form-control" placeholder="Tipo" value="<%= tipopc %>">
            </div>
            <div class="form-group">
                <label for="detalle" class="form-control-label">Detalle</label>
                <input type="text" id="detalle" class="form-control" placeholder="Detalle" value="<%= detalle %>">
            </div>
            <div class="form-group">
                <label for="dias" class="form-control-label">Dias</label>
                <input type="number" id="dias" class="form-control" placeholder="Dias" value="<%= dias %>">
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio09';
    </script>

    <script src="{{ asset('cajas/build/TipoOpciones.js') }}"></script>
@endpush
