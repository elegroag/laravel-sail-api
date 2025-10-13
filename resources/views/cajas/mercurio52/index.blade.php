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
                <label for="codmen" class="form-control-label">Menu</label>
                <input type="text" id="codmen" name="codmen" class="form-control" placeholder="Menu">
            </div>
            <div class="form-group">
                <label for="detalle" class="form-control-label">Detalle</label>
                <input type="text" id="detalle" name="detalle" class="form-control" placeholder="Detalle">
            </div>
            <div class="form-group">
                <label for="codare" class="form-control-label">Area</label>
                <select id="codare" name="codare" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio55->find() as $row)
                        <option value="{{ $row->codare }}">{{ $row->detalle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="url" class="form-control-label">Url Movil</label>
                <input type="text" id="url" name="url" class="form-control" placeholder="Url">
            </div>
            <div class="form-group">
                <label for="tipo" class="form-control-label">Tipo</label>
                <select id="tipo" name="tipo" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio51->getTipoArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="estado" class="form-control-label">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach ($Mercurio51->getEstadoArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio52';
    </script>

    <script src="{{ asset('cajas/build/Mercurio52.js') }}"></script>
@endpush
