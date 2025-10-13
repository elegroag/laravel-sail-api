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
            <div class="form-group">
                <label for="codsed" class="form-control-label">Codigo</label>
                <input type="hidden" id="codsed" name="codsed" class="form-control">
                <input type="number" id="nit" name="nit" class="form-control" placeholder="Nit">
            </div>
            <div class="form-group">
                <label for="razsoc" class="form-control-label">Razon Social</label>
                <input type="text" id="razsoc" name="razsoc" class="form-control" placeholder="Razon social">
            </div>
            <div class="form-group">
                <label for="direccion" class="form-control-label">Direccion</label>
                <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Direccion">
            </div>
            <div class="form-group">
                <label for="email" class="form-control-label">Email</label>
                <input type="text" id="email" name="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="celular" class="form-control-label">Celular</label>
                <input type="number" id="celular" name="celular" class="form-control" placeholder="Celular">
            </div>
            <div class="form-group">
                <label for="codcla" class="form-control-label">Clasificacion</label>
                <select id="codcla" name="codcla" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio67->find() as $row)
                        <option value="{{ $row->codcla }}">{{ $row->detalle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="detalle" class="form-control-label">Detalle</label>
                <input type="number" id="detalle" name="detalle" class="form-control" placeholder="Detalle">
            </div>
            <div class="form-group">
                <label for="lat" class="form-control-label">Latitud</label>
                <input type="text" id="lat" name="lat" class="form-control" placeholder="Latitud">
            </div>
            <div class="form-group">
                <label for="log" class="form-control-label">Longitud</label>
                <input type="text" id="log" name="log" class="form-control" placeholder="Longitud">
            </div>
            <div class="form-group">
                <label for="archivo" class="form-control-label">Archivo</label>
                <div class='custom-file'>
                    <input type='file' class='custom-file-input' id='archivo' name='archivo'>
                    <label class='custom-file-label' for='customFileLang'>Select file</label>
                </div>
            </div>
            <div class="form-group">
                <label for="estado" class="form-control-label">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio65->getEstadoArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio65';
    </script>

    <script src="{{ asset('cajas/build/Mercurio65.js') }}"></script>
@endpush
