@extends('layouts.dash')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')  
<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
@endpush

@section('content')
<div id='boneLayout'></div>

<div class="card mb-0">
    <div class="card-header">
        <div class="col-md-auto d-flex mr-auto">
            <button type="button" class="btn btn-primary align-self-center" id="btn_consulta_nomina">Consultar</button>
        </div>
    </div>
    <div class="card-body">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
        <div class="d-flex justify-content-center">
            <div class="form-group">
                <label for="periodo" class="form-control-label">Periodo</label>
                <input type="text" id="periodo" name="periodo" class="form-control" placeholder="Periodo">
            </div>
        </div>
        </form>
        <div id='consulta' class='table-responsive'></div>
    </div>
</div>
<script src="{{ asset('mercurio/build/NominasEmpresa.js') }}"></script>
@endsection


