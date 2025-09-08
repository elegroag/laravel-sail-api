@extends('layouts.dash')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
@endpush

@section('content')
<div class="card mb-0">
    <div class="card-body">
        <form id="form"
              method="POST"
              action="#"
              class="validation_form"
              autocomplete="off"
              novalidate>
            @csrf

            <div class="row">
                <div class="col-md-6 ml-auto">
                    <div class="form-group">
                        <label for="cedtra" class="form-control-label">Cédula</label>
                        <input type="number"
                               name="cedtra"
                               id="cedtra"
                               class="form-control"
                               placeholder="Cédula"
                               value="{{ old('cedtra') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nombre" class="form-control-label">Nombre</label>
                        <input type="text"
                               name="nombre"
                               id="nombre"
                               class="form-control"
                               placeholder="Nombre"
                               disabled
                               value="{{ old('nombre') }}">
                    </div>
                </div>

                <div class="col-md-auto d-flex m-auto">
                    <button type="button"
                            class="btn btn-icon btn-primary align-self-center"
                            onclick="buscar_trabajador();">
                        <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                        <span class="btn-inner--text">Buscar Trabajador</span>
                    </button>

                    <button type="button"
                            class="btn btn-icon btn-warning align-self-center"
                            onclick="window.location.reload();">
                        <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                        <span class="btn-inner--text">Reiniciar</span>
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 ml-auto">
                    <div class="form-group">
                        <label for="codest" class="form-control-label">Motivo</label>
                        <select name="codest"
                                id="codest"
                                class="form-control"
                                {{ empty($codest) ? 'disabled' : '' }}>
                            @foreach($codest ?? [] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="fecafi" value="{{ old('fecafi') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecret" class="form-control-label">Fecha Retiro</label>
                        <input type="date"
                               name="fecret"
                               id="fecret"
                               class="form-control"
                               placeholder="Fecha Retiro"
                               value="{{ old('fecret') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="archivo" class="form-control-label">Archivo</label>
                        <input type="file"
                               name="archivo"
                               id="archivo"
                               class="form-control"
                               accept="application/pdf, image/*">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 ml-auto">
                    <div class="form-group">
                        <label for="nota" class="form-control-label">Nota</label>
                        <textarea name="nota"
                                  id="nota"
                                  rows="5"
                                  class="form-control">{{ old('nota') }}</textarea>
                    </div>
                </div>

                <div class="col-md-auto d-flex m-auto">
                    <button type="button"
                            class="btn btn-icon btn-primary align-self-center"
                            id="bt_novedad_retiro">
                        <span class="btn-inner--icon"><i class="ni ni-paper-diploma"></i></span>
                        <span class="btn-inner--text">Retirar Trabajador</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('Mercurio/consultasempresa/consultasempresa.build.js') }}"></script>
@endsection