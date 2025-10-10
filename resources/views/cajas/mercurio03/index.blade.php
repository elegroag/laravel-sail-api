@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    <script type="text/template" id='tmp_form'>
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="row justify-content-between">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="codfir" class="form-control-label">Firma</label>
                        <input type="text" name="codfir" class="form-control" placeholder="Firma" value="<%=codfir%>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nombre" class="form-control-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="<%=nombre%>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cargo" class="form-control-label">Cargo</label>
                        <input type="text" name="cargo" class="form-control" placeholder="Cargo" value="<%=cargo%>" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="archivo" class="form-control-label">Archivo</label>
                        <div class='custom-file'>
                            <input type='file' class='custom-file-input' id='archivo' name='archivo'>
                            <label class='custom-file-label' for='customFileLang'>Select file</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="form-control-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<%=email%>" />
                    </div>
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'mercurio03';
    </script>

    <script src="{{ asset('cajas/build/Firmas.js') }}"></script>
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