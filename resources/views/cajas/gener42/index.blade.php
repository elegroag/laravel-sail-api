@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div class="row">
                        <div class="col-3"> </div>
                        <div class="col-6">
                            <form id="form" method="#" class="validation_form" autocomplete="off" novalidate>
                            <div class="form-group">
                                <label for="tipfun" class="form-control-label">Usuario</label>
                                <select id="usuario" name="usuario" class="form-control" required>
                                    <option value="">Seleccione un usuario</option>
                                    @foreach ($gener02 as $item)
                                        <option value="{{ $item->usuario }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="row card-body">
                        <div id='nopermite' class='card col-5' style='overflow: auto; height: 500px;'> </div>
                        <div class='col-2' style='align-self: center;'>
                            <button type="button" class="btn btn-primary btn-lg" toggle-event='agregar' style='width: 100%;'>
                                Agregar
                                <i class="fas fa-arrow-right"></i>
                            </button> <br><br>
                            <button type="button" class="btn btn-primary btn-lg" toggle-event='quitar' style='width: 100%;'>
                                <i class="fas fa-arrow-left"></i>
                                Quitar
                            </button>
                        </div>
                        <div id='permite' class='card col-5' style='overflow: auto; height: 500px;'> </div>
                    </div>          
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
            <label for="coddoc" class="form-control-label">Documento</label>
            <input type="text" id="coddoc" name="coddoc" class="form-control" placeholder="Documento" required value="<%= coddoc %>">
        </div>
        <div class="form-group">
            <label for="detalle" class="form-control-label">Detalle</label>
            <input type="text" id="detalle" name="detalle" class="form-control" placeholder="Detalle" required value="<%= detalle %>">
        </div>
        </form>
    </script>

    <script>
        window.ServerController = 'gener42';
    </script>

    <script src="{{ asset('cajas/build/Permisos.js') }}"></script>
@endpush