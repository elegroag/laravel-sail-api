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
                <div class="card-body p-0 m-3">
                    <div class="col-12">
                        <form id="form" class="validation_form" autocomplete="off" novalidate>
                            <div class="row">
                                <div class="col-md-4 ml-auto">
                                    <div class="form-group">
                                        <label for="fecini" class="form-control-label">Fecha Inicial</label>
                                        <input type="text" id="fecini" name="fecini" class="form-control" placeholder="Fecha Inicial" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecfin" class="form-control-label">Fecha Final</label>
                                        <input type="text" id="fecfin" name="fecfin" class="form-control" placeholder="Fecha Final" required>
                                    </div>
                                </div>
                                <div class="col-md-auto d-flex mr-auto pt-4">
                                    <button type="button" class="btn btn-primary align-self-center" data-toggle="consulta_indicadores">Consultar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12">
                        <div id='consulta' class='table-responsive'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

    <script src="{{ asset('cajas/build/Indicadores.js') }}"></script>
@endpush



