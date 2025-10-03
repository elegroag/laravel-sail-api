@extends('layouts.dash')

@push('scripts')
<script src="{{ asset('Mercurio/consultasempresa/consultasempresa.build.js') }}"></script>
@endpush

@section('title', 'Certificado para Trabajador')


@section('content')
@php 
$tipo = [
    "A" => "Certificado Afiliacion Principal",
    "I" => "Certificacion Con Nucleo",
    "T" => "Certificacion de Multiafiliacion",
    "P" => "Reporte trabajador en planillas"
];
@endphp
<div class="card mb-0">
    <div class="card-body">
        <form id="form" class="validation_form" action="{{ url('mercurio/subsidioemp/certificado_para_trabajador') }}" method="POST" autocomplete="off" novalidate>
        <div class="row">
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="cedtra" class="form-control-label">Trabajador</label>
                    <select name="cedtra" id="cedtra" class="form-control">
                        @foreach ($trabajadores as $trabajador)
                            <option value="{{ $trabajador->cedula }}">{{ $trabajador->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 ml-auto">
                <div class="form-group">
                    <label for="tipo" class="form-control-label">Tipo</label>
                    <select name="tipo" id="tipo" class="form-control">
                        @foreach ($tipo as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-auto d-flex mr-auto">
                <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_certificado_afiliacion">
                    <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                    <span class="btn-inner--text">Generar Certificado</span>
                </button>
            </div>
        </div>
        </form>
    </div>
</div>

@endsection