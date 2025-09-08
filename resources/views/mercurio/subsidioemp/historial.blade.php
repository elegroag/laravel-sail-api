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
<div class="card-body">

    <div class="nav-wrapper">
        <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-bs-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Afiliaciones Trabajador
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-bs-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false">
                    <i class="fas fa-user-friends mr-2"></i>Novedades Retiro
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" data-bs-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">
                    <i class="fas fa-child mr-2"></i>Datos Basicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-4-tab" data-bs-toggle="tab" href="#tabs-icons-text-4" role="tab" aria-controls="tabs-icons-text-4" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Afiliaciones Conyuges
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-5-tab" data-bs-toggle="tab" href="#tabs-icons-text-5" role="tab" aria-controls="tabs-icons-text-5" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Afiliaciones Beneficiarios
                </a>
            </li>
        </ul>
    </div>
    <div class="card shadow">
        <div class="card-body pt-0">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <hr />
                    <div class="row">
                        {!! $html_afiliacion !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                    <hr />
                    <div class="row">
                        {!! $html_retiro !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                    <hr />
                    <div class="row">
                        {!! $actualizacion_basico !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-4" role="tabpanel" aria-labelledby="tabs-icons-text-4-tab">
                    <hr />
                    <div class="row">
                        {!! $html_afiliacion_conyuge !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="tabs-icons-text-5" role="tabpanel" aria-labelledby="tabs-icons-text-5-tab">
                    <hr />
                    <div class="row">
                        {!! $html_afiliacion_beneficiario !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection