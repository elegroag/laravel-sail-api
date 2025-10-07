@extends('layouts.cajas')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">
@endpush

@push('scripts')

<script type="text/template" id='tmp_form'>
    <form id="form" class="validation_form" autocomplete="off" novalidate>
        <div class="row justify-content-between">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="codapl" class="form-control-label">Aplicativo</label>
                    <input type="text" name="codapl" class="form-control" placeholder="Aplicativo" value="<%=codapl%>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="email" class="form-control-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<%=email%>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="clave" class="form-control-label">Clave</label>
                    <input type="text" name="clave" class="form-control" placeholder="Clave" value="<%=clave%>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="path" class="form-control-label">Path</label>
                    <input type="text" name="path" class="form-control" placeholder="Path" value="<%=path%>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ftpserver" class="form-control-label">ftpserver</label>
                    <input type="text" name="ftpserver" class="form-control" placeholder="ftpserver" value="<%=ftpserver%>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="pathserver" class="form-control-label">pathserver</label>
                    <input type="text" name="pathserver" class="form-control" placeholder="pathserver" value="<%=pathserver%>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="userserver" class="form-control-label">userserver</label>
                    <input type="text" name="userserver" class="form-control" placeholder="userserver" value="<%=userserver%>" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="passserver" class="form-control-label">passserver</label>
                    <input type="text" name="passserver" class="form-control" placeholder="passserver" value="<%=passserver%>" />
                </div>
            </div>
        </div>
    </form>
</script>

<script>
    window.ServerController = 'mercurio01';
</script>

@include("partials.modal_generic", [
    "titulo" => 'Configuración básica',
    "contenido" => '',
    "evento" => 'data-toggle="guardar"',
    "btnShowModal" => 'btCaptureModal',
    "idModal" => 'captureModal'])

<script src="{{ asset('cajas/build/Basicas.js') }}"></script>
@endpush

@section('content')
<div class="card border-0 m-2">
    <div class="card-header">
        <h4 class="font-weight-bold">{{ $title }}</h4>
    </div>
    <div id='consulta' class='table-responsive'></div>
    <div id='paginate' class='card-footer py-4'></div>
</div>
@endsection
