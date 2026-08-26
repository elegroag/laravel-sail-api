@extends('layouts.cajas')

@section('content')
@include('cajas/templates/tmp_header_adapter', [
    'sub_title' => $title,
    'filtrar' => false,
    'listar' => false,
    'salir' => false,
    'add' => false,
])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body m-3">
                    <div class="text-center mb-4">
                        <h5 class="mb-1">Genera el certificado para trabajador</h5>
                        <p class="text-muted mb-0">Ingrese el NIT de la empresa, seleccione el trabajador y el tipo de certificado.</p>
                    </div>

                    <form
                        id="form"
                        class="validation_form"
                        action="{{ route('consulta.generarCertificadoTrabajador') }}"
                        method="POST"
                        autocomplete="off"
                        novalidate
                    >
                        @csrf
                        <div class="row g-3 align-items-end justify-content-center">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="nit" class="form-control-label">NIT Empresa</label>
                                    <input type="text" name="nit" id="nit" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="cedtra" class="form-control-label">Trabajador</label>
                                    <select name="cedtra" id="cedtra" class="form-control" required data-minimum-results-for-search="0">
                                        <option value="">— Ingrese NIT primero —</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="tipo" class="form-control-label">Tipo</label>
                                    <select name="tipo" id="tipo" class="form-control" required>
                                        @foreach ($tipos as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
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
</div>
@endsection

@push('scripts')
<script>
    window.ConsultaCertificado = {
        urlTrabajadores: @json(route('consulta.trabajadoresPorNit')),
    };
</script>
<script src="{{ versioned_asset('cajas/build/Consulta.js') }}"></script>
@endpush
