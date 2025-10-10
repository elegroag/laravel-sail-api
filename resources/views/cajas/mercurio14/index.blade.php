@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])
<div id="boneLayout"></div>
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

    <script id='tmp_layout' type="text/template">
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
    </script>

    <script id='tmp_form' type="text/template">
        <form id="form" method='POST'>
            <div class="row justify-content-start">
                <div class="form-group mb-2" style="width: 50%;">
                    <label for="tipopc" class="form-control-label">Tipo afiliación</label>
                    <select id="tipopc" name="tipopc" class="form-control" required>
                        <option value="">Selecciona aquí...</option>
                        @foreach ($tipopc as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2" style="width: 50%;">
                    <label for="coddoc" class="form-control-label">Documento</label>
                    <select id="coddoc" name="coddoc" class="form-control" required>
                        <option value="">Selecciona aquí...</option>
                        @foreach ($coddoc as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2" style="width: 50%;">
                    <label for="tipsoc" class="form-control-label">Tipo sociedad</label>
                    <select id="tipsoc" name="tipsoc" class="form-control" required>
                        <option value="">Selecciona aquí...</option>
                        @foreach ($tipsoc as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2" style="width: 140px;">
                    <label for="obliga" class="form-control-label">Obligatorio</label>
                    <select id="obliga" name="obliga" class="form-control" required>
                        <option value="">Selecciona aquí...</option>
                        @foreach (array('N' => 'NO', 'S' => 'SI') as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2" style="width: 140px;">
                    <label for="auto_generado" class="form-control-label">Auto generado</label>
                    <select id="auto_generado" name="auto_generado" class="form-control" required>
                        <option value="">Selecciona aquí...</option>
                        @foreach (array('0' => 'NO', '1' => 'SI') as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2" style="width: 100%;">
                    <label for="nota" class="form-control-label">Nota observaciones:</label>
                    <textarea id="nota" name='nota' class="form-control" placeholder="NOTA" rows="3""></textarea>
                </div>
            </div>
        </form>
        
    </script>

    <script>
        window.ServerController = 'mercurio14';
    </script>

    <script src="{{ asset('cajas/build/DocureqEmpresas.js') }}"></script>
@endpush