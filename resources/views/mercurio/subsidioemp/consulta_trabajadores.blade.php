
@extends('layouts.dash')


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
<style>
    #dataTable {
        font-size: 0.7rem;
    }
    #dataTable thead {
        background-color: #f0f0f0;
    }
    #dataTable th {
        padding: 0.3rem;
        text-align: left;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    #dataTable td {
        padding: 0.3rem;
        text-align: center;
        vertical-align: middle;
    }
</style>
@endpush


@push('scripts')
<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<script type="text/template" id="templateConyuge">
    @include('mercurio/subsidio/tmp/tmp_conyuge')
</script>

<script type="text/template" id="templateBeneficiario">
    @include('mercurio/subsidio/tmp/tmp_beneficiario')
</script>    
@endpush

@section('content')
<div class="card mb-0">
    <div class="card-header">
        <div class="col-md-auto d-flex mr-auto">
            <button type="button" class="btn btn-primary align-self-center" id="bt_consulta_trabajadores">Consultar</button>
        </div>
    </div>
    <div class="card-body">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
            <div class="d-flex justify-content-center">
                <div class="form-group">
                    <label for="estado" class="form-control-label">Indica el estado de afiliación</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="A">ACTIVOS</option>
                        <option value="I">INACTIVOS</option>
                        <option value="T">TODOS</option>
                    </select>
                </div>
            </div>
        </form>
        <div id="consulta" class="table-responsive"></div>
    </div>
</div>

<div class="modal fade" id="modalConsultaNucleo" tabindex="-1" role="dialog" aria-labelledby="notice" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">
                    <i class="nc-icon nc-simple-remove"></i>
                </button>
                <h5 class="modal-title" id='mdl_set_title'>Consulta nucleo familiar</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="render_conyuges"></div>
                    <div class="col-md-12" id="render_beneficiarios"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="mdl_set_footer">
                <button type="button" class="btn btn-info btn-round" data-bs-dismiss="modal" id="mdl_set_button">Continuar!</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('mercurio/build/TrabajadoresEmpresa.js') }}"></script>

@endsection
