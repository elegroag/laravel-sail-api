@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => false])
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
        "titulo" => 'Reasignación',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    <script id='tmp_form' type="text/template">
        <div class="card mb-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 center">
                        <div class="form-group">
                            <label for="accion" class="form-control-label">Accion a Realizar</label>
                            <select id="accion" name="accion" class="form-control" onchange="cambiarAccion()">
                                <option value="">Seleccione</option>
                                <option value="C">CONSULTA</option>
                                <option value="P">PROCESO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <form id="form_proceso" class="validation_form" autocomplete="off" novalidate>
                    <div id='procesar_form'>
                        <div class="row justify-content-around">
                            <div class='col-12'>
                                <div class="alert alert-success m-3" role="alert" style='padding:3px'>
                                    <h4 class="text-white" style='padding:0px'>Proceso de Reasignación</h4>
                                    <p style='font-size: 14px;'>Esta opción se encarga de reasignar todas las solicitudes en estado <strong>PENDIENTE</strong> cuya fecha de solicitud se encuentre en el intervalo de fechas escogido, la reasignacion se realiza del usuario origen al usuario destino.</p>
                                </div>
                            </div>
                            <div class="col-md-4 ml-auto">
                                <div class="form-group">
                                    <label for="tipopc_proceso" class="form-control-label">Opción</label>
                                    <select id="tipopc_proceso" name="tipopc_proceso" class="form-control">
                                        <option value="">Seleccione</option>
                                        @foreach ($Mercurio09->find() as $row)
                                            <option value="{{ $row->tipopc }}">{{ $row->detalle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="usuori" class="form-control-label">Usuario Origen</label>
                                    <select id="usuori" name="usuori" class="form-control">
                                        <option value="">Seleccione</option>
                                        @foreach ($Gener02->find("usuario in (select usuario from mercurio08)") as $row)
                                            <option value="{{ $row->usuario }}">{{ $row->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="usudes" class="form-control-label">Usuario Destino</label>
                                    <select id="usudes" name="usudes" class="form-control">
                                        <option value="">Seleccione</option>
                                        @foreach ($Gener02->find("usuario in (select usuario from mercurio08)") as $row)
                                            <option value="{{ $row->usuario }}">{{ $row->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fecini" class="form-control-label">Fecha Inicio</label>
                                    <input type="date" id="fecini" name="fecini" class="form-control" placeholder="Fecha Inicio">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                                    <input type="date" id="fecfin" name="fecfin" class="form-control" placeholder="Fecha Final">
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button type="button" class="btn btn-primary align-self-center" id='btnProcesoReasignarMasivo'>Procesar</button>
                            </div>
                        </div>
                    </div>
                </form>

                <form id="form" class="validation_form" autocomplete="off" novalidate>
                    <div class="row" id='consultar_form'>
                        <div class="col-md-4 ml-auto">
                            <div class="form-group">
                                <label for="tipopc" class="form-control-label">Opcion</label>
                                <select id="tipopc" name="tipopc" class="form-control">
                                    <option value="">Seleccione</option>
                                    @foreach ($Mercurio09->find() as $row)
                                        <option value="{{ $row->tipopc }}">{{ $row->detalle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="usuario" class="form-control-label">Usuario</label>
                                <select id="usuario" name="usuario" class="form-control">
                                    <option value="">Seleccione</option>
                                    @foreach ($Gener02->find("usuario in (select usuario from mercurio08)") as $row)
                                        <option value="{{ $row->usuario }}">{{ $row->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto d-flex mr-auto">
                            <button type="button" class="btn btn-primary align-self-center" id='btnTraerDatos'>Consultar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </script>

    <script>
        window.ServerController = 'reasigna';
    </script>

    <script src="{{ asset('cajas/build/Reasigna.js') }}"></script>
@endpush
