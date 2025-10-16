@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    <script>
        window.ServerController = 'consulta';
    </script>

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal'])
    
    <script type="text/template" id='tmp_form'>
        <form id="form_consultar" class="validation_form" autocomplete="off" novalidate>
            <div class="row" id='consultar_form'>
                <div class="col-md-4 ml-auto">
                    <div class="form-group">
                        <label for="tipopc" class="form-control-label">Opcion</label>
                        @component('components.select-field', [
                            'id' => 'tipopc',
                            'name' => 'tipopc',
                            'options' => $data_mercurio09,
                            'events' => 'data-toggle="cambiar-accion"',
                        ])@endcomponent
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="usuario" class="form-control-label">Usuario</label>
                        @component('components.select-field', [
                            'id' => 'usuario',
                            'name' => 'usuario',
                            'options' => $data_usuarios,
                            'events' => 'data-toggle="cambiar-accion"',
                        ])@endcomponent
                    </div>
                </div>
                <div class="col-md-auto d-flex mr-auto">
                    <button type="button" class="btn btn-primary align-self-center" id='btnTraerDatos'">Consultar</button>
                </div>
            </div>
        </form>
    </script>

    <script src="{{ asset('cajas/build/Reasigna.js') }}"></script>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0 m-3">
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <div class="form-group">
                                <label for="accion" class="form-control-label">Accion a realizar</label>
                                @component('components.select-field', [
                                    'id' => 'accion',
                                    'name' => 'accion',
                                    'options' => $accion,
                                    'events' => 'data-toggle="cambiar-accion"',
                                ])@endcomponent
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <form id="form_proceso" class="validation_form" autocomplete="off" novalidate>
                            <div class="row justify-content-around">
                                <div class='col-12'>
                                    <div class="alert alert-success m-3" role="alert" style='padding:3px'>
                                        <h4 class="text-white" style='padding:0px'>Proceso de Reasignación</h4>
                                        <p style='font-size: 14px;'>Esta opción se encarga de reasignar todas las solicitudes
                                            en estado<strong> PENDIENTE</strong> cuya fecha de solicitud se encuentre en el intervalo de fechas
                                            escogido, la reasignacion se realiza del usuario origen al usuario destino .</p>
                                    </div>
                                </div>
                                <div class="col-md-4 ml-auto">
                                    <div class="form-group">
                                        <label for="tipopc" class="form-control-label">Opción</label>
                                        @component('components.select-field', [
                                            'id' => 'tipopc_proceso',
                                            'name' => 'tipopc_proceso',
                                            'options' => $data_mercurio09,
                                            'events' => 'data-toggle="cambiar-accion"',
                                        ])@endcomponent
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="usuori" class="form-control-label">Usuario Origen</label>
                                        @component('components.select-field', [
                                            'id' => 'usuori',
                                            'name' => 'usuori',
                                            'options' => $data_usuarios,
                                            'events' => 'data-toggle="cambiar-accion"',
                                        ])@endcomponent
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="usudes" class="form-control-label">Usuario Destino</label>
                                        @component('components.select-field', [
                                            'id' => 'usudes',
                                            'name' => 'usudes',
                                            'options' => $data_usuarios,
                                            'events' => 'data-toggle="cambiar-accion"',
                                        ])@endcomponent
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="fecini" class="form-control-label">Fecha Inicio</label>
                                        <input type="date" id="fecini" name="fecini" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="fecfin" class="form-control-label">Fecha Final</label>
                                        <input type="date" id="fecfin" name="fecfin" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button type="button" class="btn btn-primary align-self-center" id='btnProcesoReasignarMasivo'>Procesar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id='consulta' class='table-responsive'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
