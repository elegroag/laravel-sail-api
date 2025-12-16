@extends('layouts.bone')

@push('scripts')
<script src="{{ asset('mercurio/build/GeneradorCertificado.js') }}"></script>
@endpush

@section('title', 'Certificados de Trabajador')

@section('content')
<div class="col-12 mt-3">
    <div class="card mb-0">
        <div class="card-body">
            <div class="text-center mb-4">
                <h5 class="mb-1">Genera el certificado de trabajador</h5>
                <p class="text-muted mb-0">Selecciona el trabajador y el tipo de certificado, luego genera el documento.</p>
            </div>

            <form id="form" 
                class="validation_form" 
                autocomplete="off" 
                novalidate
                action="{{ url('mercurio/subsidio/certificado_afiliacion') }}" 
                method="POST" 
                target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-5 ml-auto">
                        <div class="form-group">
                            @component('components.select-field', 
                            [   'name' => 'tipo', 
                                'id' => 'tipo', 
                                'class' => 'form-control', 
                                'options' => $tipo, 
                                'dummy' => 'Seleccione',
                                'label' => 'Tipo certificado'
                            ])
                            @endcomponent
                        </div>
                    </div>
                    <div class="col-md-auto d-flex mr-auto pt-4">
                        <button type="button" class="btn btn-icon btn-primary align-self-center" id="bt_certificado_afiliacion">
                            <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                            <span class="btn-inner--text">Generar Certificado</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection