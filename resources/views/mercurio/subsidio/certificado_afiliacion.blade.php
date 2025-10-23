@extends('layouts.bone')

@section('content')
<div class="col-12 mt-3">
    <div class="card mb-0">
        <div class="card-header p-3">
           <h5 class="mb-0">Certificado de Afiliación</h5>
        </div>
        <div class="card-body">
            <form id="form" class="validation_form" autocomplete="off" novalidate>
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

@push('scripts')
<script src="{{ asset('mercurio/build/CertificadoAfiliacion_view.js') }}"></script>
@endpush
