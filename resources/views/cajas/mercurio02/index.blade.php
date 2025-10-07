@extends('layouts.cajas')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
<script type="text/template" id='tmp_form'>
    <form id="form" class="validation_form" autocomplete="off" novalidate>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="codcaj" class="form-control-label">Código Caja</label>
                    <input type="text" name="codcaj" class="form-control" placeholder="Código Caja" value="<%= codcaj %>" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nit" class="form-control-label">NIT</label>
                    <input type="text" name="nit" class="form-control" placeholder="NIT" value="<%= nit %>" />
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="razsoc" class="form-control-label">Razón Social</label>
                    <input type="text" name="razsoc" class="form-control" placeholder="Razón Social" value="<%= razsoc %>" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sigla" class="form-control-label">Sigla</label>
                    <input type="text" name="sigla" class="form-control" placeholder="Sigla" value="<%= sigla %>" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="form-control-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" value="<%= email %>" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="direccion" class="form-control-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Dirección" value="<%= direccion %>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="telefono" class="form-control-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono" value="<%= telefono %>" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="codciu" class="form-control-label">Ciudad</label>
                    <select name="codciu" class="form-control select2">
                        @foreach ($ciudades as $key => $ciudad)
                            <option value="{{ $key }}" <%= codciu == '{{ $key }}' ? 'selected' : '' %>>{{ $ciudad }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pagweb" class="form-control-label">Sitio Web</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                        <input type="text" name="pagweb" class="form-control" placeholder="https://" value="<%= pagweb %>" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pagfac" class="form-control-label">Facebook</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                        <input type="text" name="pagfac" class="form-control" placeholder="Usuario" value="<%= pagfac %>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pagtwi" class="form-control-label">Twitter</label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" name="pagtwi" class="form-control" placeholder="Usuario" value="<%= pagtwi %>" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pagyou" class="form-control-label">YouTube</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                        <input type="text" name="pagyou" class="form-control" placeholder="Canal" value="<%= pagyou %>" />
                    </div>
                </div>
            </div>
        </div>
    </form>      
</script>

<script>
    window.ServerController = 'mercurio02';
</script>

@include("partials.modal_generic", [
    "titulo" => 'Configuración básica',
    "contenido" => '',
    "evento" => 'data-toggle="guardar"',
    "btnShowModal" => 'btCaptureModal',
    "idModal" => 'captureModal'])

<script src="{{ asset('cajas/build/DatosCaja.js') }}"></script>
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
