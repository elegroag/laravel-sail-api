@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    <script>
        window.ServerController = 'admproductos';
    </script>

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal'])

    <script src="{{ asset('cajas/build/AdmProductos.js') }}"></script>
@endpush

@section('content')
    @include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])
    
    <div class="container-fluid mt--9 pb-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-green-blue p-1"></div>
                    <div class="card-body p-0 m-3">
                        <div id='consulta' class='table-responsive'>
                            <table class="table-sm align-items-center mt-2" id='datatable' style="width:100%"></table>
                        </div>      
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
