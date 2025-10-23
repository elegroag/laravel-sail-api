@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="col-12 mt-3">
    <div class="card mb-0">
        <div class="card-header p-3">
            <div class="nav-wrapper p-0">
                <ul class="nav nav-pills" id="tabs-icons-text" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 active"
                            id="tabsTrabajadorTab"
                            data-bs-toggle="tab"
                            href="#tabsTrabajador"
                            role="tab"
                            aria-controls="tabsTrabajador"
                            aria-selected="true">
                            <i class="fas fa-user-tie  mr-2"></i>Trabajador
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0"
                            id="tabsConyugeTab"
                            data-bs-toggle="tab"
                            href="#tabsConyuge"
                            role="tab"
                            aria-controls="tabsConyuge"
                            aria-selected="false">
                            <i class="fas fa-user-friends mr-2"></i>Conyuges
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link mb-sm-3 mb-md-0"
                            id="tabsBeneficiarioTab"
                            data-bs-toggle="tab"
                            href="#tabsBeneficiario"
                            role="tab"
                            aria-controls="tabsBeneficiario"
                            aria-selected="false">
                            <i class="fas fa-child mr-2"></i>Beneficiarios
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0 m-3">
            <div id="myTabContent"></div>
        </div>        
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <script type="text/template" id="tmp_layout">
        <div
            class="tab-pane fade show active p-2"
            id="tabsTrabajador"
            role="tabpanel"
            aria-labelledby="tabsTrabajadorTab">
        </div>

        <div
            class="tab-pane fade p-2"
            id="tabsConyuge"
            role="tabpanel"
            aria-labelledby="tabsConyugeTab">
        </div>

        <div
            class="tab-pane fade p-2"
            id="tabsBeneficiario"
            role="tabpanel"
            aria-labelledby="tabsBeneficiarioTab">
        </div>
    </script>

    <script type="text/template" id="templateTrabajador">
        @include('mercurio/subsidio/tmp/tmp_nucleo')
    </script>

    <script type="text/template" id="templateConyuge">
        @include('mercurio/subsidio/tmp/tmp_conyuge')
    </script>

    <script type="text/template" id="templateBeneficiario">
        @include('mercurio/subsidio/tmp/tmp_beneficiario')
    </script>

    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'subsidio';
    </script>

    <script src="{{ asset('mercurio/build/ConsultaNucleo.js') }}"></script>
@endpush