@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <style>
        .table td {
            font-size: 0.91rem;
        }

        .btn-link-file {
            border-radius: 1px;
            margin-top: 5px;
            padding: 8px 5px !important;
            border: 0px;
            cursor: pointer;
            background-color: #589d62 !important;
            color: #fff !important;
            border-radius: 5px;
            font-size: 13px;
        }

        .btn-link-file:hover,
        .btn-link-file:focus,
        .btn-link-file:active {
            border: 0px;
            cursor: pointer;
        }

        .text-muted .list-group-item {
            padding: 0.2rem 1rem;
        }

        .form-control-label {
            font-size: .81rem;
            margin-bottom: 3px;
        }

        select.form-control,
        input.form-control {
            margin: 3px 0px 0px 3px;
            padding: 4px 6px;
            border-radius: 0px;
            height: initial;
            min-height: 20px;
            background-color: #fffeee;
        }

        label.error {
            color: red;
            font-size: 0.81rem;
        }

        .select2-container .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-search--dropdown .select2-search__field {
            padding: 0.2rem 0.3rem;
            font-size: 13px;
        }

        .list-group-item.active {
            color: #222;
            border-color: #c5e1c9;
            background-color: #c5e1c9;
        }

        h4 {
            color: #589d62;
            font-size: 1.2rem;
        }

        .input-group-append .btn-sm {
            height: 31px;
            top: 3.4px;
        }

        p {
            font-size: 0.85em;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script type="text/template" id="tmp_detalle_aplicado">
        <div class="card-header mb-1 pt-3">
            <div id="botones" class='row'>
                <div class='col-md-10'><h3>Detalles del afiliado</h3></div>
            </div>
        </div>
        <div class="card-body pt-1">
            <div class='row pl-lg-12 pb-3'>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Cedula trabajador</label>
                    <p class='pl-2 description'><%=cedtra%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Estado</label>
                    <p class='pl-2 description'><%=estado_detalle%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Trabajador</label>
                    <p class='pl-2 description'><%=trabajador.prinom + ' '+ trabajador.segnom + ' ' + trabajador.priape + ' ' + trabajador.segape%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Beneficiario</label>
                    <p class='pl-2 description'><%=(beneficiario)? beneficiario.prinom+ ' '+beneficiario.segnom + ' '+beneficiario.priape+' '+beneficiario.segape : 'NO EXISTE'%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Cedula trabajador</label>
                    <p class='pl-2 description'><%=cedtra%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Documento beneficiario</label>
                    <p class='pl-2 description'><%=docben%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Empresa</label>
                    <p class='pl-2 description'><%=trabajador.nit%></p>
                </div>
                <div class='col-md-6 border-top border-right border-left border-bottom'>
                    <label class='form-control-label'>Zona</label>
                    <p class='pl-2 description'><%=trabajador.zona_detalle%></p>
                </div>
            </div>
        </div>
    </script>

    <script src="{{ asset('src/Cajas/ProductoAplicados/main.js') }}"></script>
@endpush

@section('content')
  @include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => true])
    <div class="container-fluid mt--9 pb-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-green-blue p-1">
                        <a href="{{ route('admproductos.cargue_pagos', $codser) }}" class='btn btn-md btn-warning'><i class="fas fa-plus"></i> Pagos</a>&nbsp;
                        <a href="{{ route('admproductos.lista') }}" class='btn btn-md btn-primary'><i class="fas fa-home"></i> Salir</a>&nbsp;
                    </div>
                    <div class="card-body p-0 m-3">
                        <h3 class="p-1">{{ ($servicio) ? $servicio->getServicio() : '' }}</h3>
                        <p>Lista de afiliados que han aplicado al servicio o producto</p>
                        <input style="display:none" id="codser" value="{{ $codser }}" />
                        <div class="col-md-8">
                            <table class="table table-bordered table-sm align-items-center mb-0 mt-0" id='datatable' style="width:100%"></table>
                        </div>
                        <div class="col-md-4">
                            <div class="col-auto" id="showDetalleAplicado">
                                <div class="card-body">
                                    <p class="text-center">
                                        <img src="{{ asset('img/Mercurio/consulta_aportes.jpg') }}" style="width:180px" class="img-responsive">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection