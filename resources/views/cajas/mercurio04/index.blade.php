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
</div>
@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    @include("partials.modal_generic", [
        "titulo" => 'Capturar opciones',
        "contenido" => '',
        "evento" => 'data-toggle="guardar-opciones"',
        "btnShowModal" => 'btCaptureOpciones',
        "idModal" => 'captureOpciones']
    )

    @include("partials.modal_generic", [
        "titulo" => 'Capturar ciudades',
        "contenido" => '',
        "evento" => 'data-toggle="guardar-ciudades"',
        "btnShowModal" => 'btCaptureCiudades',
        "idModal" => 'captureCiudades']
    )

    <script type="text/template" id="tmp_capture_opciones">
        <div class="container-fluid">
            <div class="row" id='div_opciones'></div>
        </div>
    </script>

    <script type="text/template" id="tmp_capture_ciudades">
        <div class="container-fluid">
            <div class="row" id='div_ciudades'></div>
        </div>
    </script>

    <script>
        window.ServerController = 'mercurio04';
    </script>

    <script src="{{ asset('cajas/build/Oficinas.js') }}"></script>
@endpush
