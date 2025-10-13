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
                <label for="codser" class="form-control-label">Servicio</label>
                <input type="hidden" id="codinf" name="codinf" value="{{ $codinf }}">
                <select id="codser" name="codser" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($_codser as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="numero" class="form-control-label">Numero</label>
                <span id='td_apertura'>
                    <select id="numero" name="numero" class="form-control">
                        <option value="">Seleccione</option>
                    </select>
                </span>
            </div>
            <div class="form-group">
                <label for="email" class="form-control-label">Email</label>
                <input type="text" id="email" name="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="nota" class="form-control-label">Nota</label>
                <input type="text" id="nota" name="nota" class="form-control" placeholder="Nota">
            </div>
            <div class="form-group">
                <label for="precan" class="form-control-label">Pregunta Cantidad?</label>
                <select id="precan" name="precan" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio59->getPrecanArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="autser" class="form-control-label">Automatico Servicio?</label>
                <select id="autser" name="autser" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio59->getAutserArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="consumo" class="form-control-label">Valida Consumo?</label>
                <select id="consumo" name="consumo" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio59->getConsumoArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="estado" class="form-control-label">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio59->getEstadoArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="archivo" class="form-control-label">Archivo</label>
                <div class='custom-file'>
                    <input type='file' class='custom-file-input' id='archivo' name='archivo'>
                    <label class='custom-file-label' for='customFileLang'>Select file</label>
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio59';
    </script>

    <script src="{{ asset('cajas/build/Mercurio59.js') }}"></script>
@endpush
