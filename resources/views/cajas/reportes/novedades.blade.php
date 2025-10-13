@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title ?? 'Novedades', 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
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
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro ?? []])

    <script id='tmp_form' type="text/template">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="row">
                <div class="col-md-2 ml-auto">
                    <div class="form-group">
                        <label for="fecini" class="form-control-label">Fecha Inicial</label>
                        <input type="date" id="fecini" name="fecini" class="form-control" placeholder="Fecha Inicial">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="fecfin" class="form-control-label">Fecha Final</label>
                        <input type="date" id="fecfin" name="fecfin" class="form-control" placeholder="Fecha Final">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipnov" class="form-control-label">Tipo Novedad</label>
                        <select id="tipnov" name="tipnov" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="1">INGRESO 1 VEZ</option>
                            <option value="2">INGRESO 2 VEZ</option>
                            <option value="5">DESAFILIACION A UNA CAJA</option>
                            <option value="7">PERDIDA DE AFILIACION CAUSA GRAVE</option>
                            <option value="8">INICIO LABORAL</option>
                            <option value="9">TERMINACION LABORAL</option>
                            <option value="10">SUSPENSION DEL CONTRATO DE TRABAJO</option>
                            <option value="11">LICENCIAS REMUNERADAS Y NO REMUNERADAS</option>
                            <option value="12">MODIFICACION DE SALARIO</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="nit" class="form-control-label">Nit</label>
                        <input type="number" id="nit" name="nit" class="form-control" placeholder="Nit">
                    </div>
                </div>
                <div class="col-md-auto d-flex mr-auto">
                    <button type="button" class="btn btn-danger align-self-center" onclick="reporte_novedades();">Reporte</button>
                </div>
            </div>
        </form>
    </script>

    <script>
        window.ServerController = 'reportes';
    </script>

    <script src="{{ asset('cajas/build/Reportes.js') }}"></script>
@endpush
