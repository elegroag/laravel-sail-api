@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')

    <script type="text/template" id='tmp_form'>
        <div class="card mb-0">
            <div class="card-body">

            </div>
        </div>
    </script>

    <script>
        window.ServerController = 'consulta';
    </script>

    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal'])

    <script src="{{ asset('cajas/build/Consulta.js') }}"></script>
@endpush

@section('content')
@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0 m-3">
                    <form id="form" action="{{route('consulta.activacion_masiva')}}" class="validation_form" autocomplete="off" novalidate>
                        <div class="row">
                            <div class="col-md-4 ml-auto">
                                <div class="form-group">
                                    <label for="fecini" class="form-control-label">Fecha Inicial</label>
                                    <input type="date" id="fecini" name="fecini" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecfin" class="form-control-label">Fecha Final</label>
                                    <input type="date" id="fecfin" name="fecfin" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex ml-auto">
                                <button type="button" class="btn btn-primary align-self-center" data-toggle="consulta_activacion_masiva">Consultar</button>
                            </div>
                        </div>
                    </form>
                    <div id='consulta' class='table-responsive'></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
