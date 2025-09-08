
@extends('layouts.dash')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
@endpush

@section('content')

<div class="card mb-0">
    <div class="card-header">
        <div class="col-md-auto d-flex mr-auto">
            <button type="button" class="btn btn-primary align-self-center" id="bt_consulta_aportes">Consultar</button>
        </div>
    </div>

    <div class="card-body">
        <form id="form" class="validation_form" autocomplete="off" novalidate>
        <div class="d-flex justify-content-center">
            <div class="form-group">
                <label for="perini" class="form-control-label">Periodo Inicial</label>
                <input type="text" id="perini" class="form-control" placeholder="Periodo Inicial">
            </div>
            <div class="form-group ml-3">
                <label for="perfin" class="form-control-label">Periodo Final</label>
                <input type="text" id="perfin" class="form-control" placeholder="Periodo Final">
            </div>
        </div>
        </form>
        <div id='consulta' class='table-responsive'></div>
    </div>
</div>


<script src="{{ asset('mercurio/build/AportesEmpresa.js') }}"></script>
@endsection