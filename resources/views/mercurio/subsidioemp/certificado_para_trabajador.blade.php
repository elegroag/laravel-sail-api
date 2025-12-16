@extends('layouts.bone')

@push('scripts')
<script src="{{ asset('mercurio/build/GeneradorCertificado.js') }}"></script>
@endpush

@section('title', 'Certificado para Trabajador')

@section('content')
@php 
$tipo = [
    "A" => "Certificación afiliación principal",
    "I" => "Certificación con núcleo",
    "T" => "Certificación de multiafiliación",
    "P" => "Reporte trabajador en planillas"
];
@endphp
<div class="card m-3">
    <div class="card-body">
        <div class="text-center mb-4">
            <h5 class="mb-1">Genera el certificado para trabajador</h5>
            <p class="text-muted mb-0">Selecciona el trabajador y el tipo de certificado, luego genera el documento.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <form 
                    id="form" 
                    class="validation_form" 
                    action="{{ url('mercurio/subsidioemp/certificado_para_trabajador') }}" 
                    method="POST" 
                    autocomplete="off" 
                    novalidate
                    target="_blank">
                    @csrf
                    <div class="row g-3 align-items-end justify-content-center">
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <label for="cedtra" class="form-control-label">Trabajador</label>
                                <select name="cedtra" id="cedtra" class="form-control">
                                    @foreach ($trabajadores as $cedula => $nombre )
                                        <option value="{{ $cedula }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <label for="tipo" class="form-control-label">Tipo</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    @foreach ($tipo as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-auto d-grid">
                            <button type="button" class="btn btn-icon btn-primary" id="bt_certificado_afiliacion">
                                <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                                <span class="btn-inner--text">Generar Certificado</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection