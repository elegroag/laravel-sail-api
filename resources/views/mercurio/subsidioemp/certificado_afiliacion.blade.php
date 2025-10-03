@extends('layouts.dash')

@section('title', 'Certificado de afiliación')

@section('content')
<div class="pb-3">
    <div class="card-body">
        <form id="form" class="validation_form" action="{{ url('mercurio/subsidioemp/certificado_afiliacion') }}" method="POST" autocomplete="off" novalidate>
        <div class="row">
            <p class="text-center">Genera el certificado de afiliación</p>
            <div class="col-md-auto d-flex m-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_certificado_afiliacion">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Generar Certificado</span>
                </button>
            </div>
        </div>
        </form>
    </div>
</div>

<script src="{{ asset('mercurio/build/ConsultasEmpresa.js') }}"></script>
@endsection