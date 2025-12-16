@extends('layouts.bone')

@section('title', 'Certificado de afiliación')

@push('scripts')
<script src="{{ asset('mercurio/build/GeneradorCertificado.js') }}"></script>
@endpush

@section('content')
<div class="card m-3">
    <div class="card-body">
        <div class="text-center mb-4">
            <h5 class="mb-1">Genera tu certificado de afiliación</h5>
            <p class="text-muted mb-0">Haz clic en el botón para generar y descargar el documento.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <form id="form" class="validation_form" action="{{ url('mercurio/subsidioemp/certificado_afiliacion') }}" method="POST" autocomplete="off" novalidate>
                    @csrf
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button type="button" class="btn btn-icon btn-primary" id="bt_certificado_afiliacion">
                            <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                            <span class="btn-inner--text">Generar Certificado</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

