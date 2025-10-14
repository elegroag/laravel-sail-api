
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
        <form id="form" method="#" class="validation_form" autocomplete="off" novalidate>
            <div class="row">
                <div class="form-group col-6">
                    <label for="codinf" class="form-control-label">Codigo</label>
                    <select name="codinf" id="codinf" class="form-control">
                        <option value="">Seleccione</option>
                        @foreach ($_infraestructura as $infraestructura)
                            <option value="{{ $infraestructura->id }}">{{ $infraestructura->codigo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="email" class="form-control-label">Email</label>
                    <input type="text" name="email" id="email" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-6">
                    <label for="telefono" class="form-control-label">Telefono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control">
                </div>
                <div class="form-group col-6">
                    <label for="nota" class="form-control-label">Nota</label>
                    <input type="text" name="nota" id="nota" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-6">
                    <label for="archivo" class="form-control-label">Archivo</label>
                    <div class='custom-file'>
                        <input type='file' class='custom-file-input' id='archivo' name='archivo'>
                        <label class='custom-file-label' for='customFileLang'>Select file</label>
                    </div>
                </div>
                <div class="form-group col-6">
                    <label for="estado" class="form-control-label">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="">Seleccione</option>
                        @foreach ($estados_array as $estado)
                            <option value="{{ $estado }}">{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio56';
    </script>

    <script src="{{ asset('cajas/build/Mercurio56.js') }}"></script>
@endpush
