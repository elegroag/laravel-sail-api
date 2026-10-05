@extends('layouts.cajas')

@push('styles')
    <style>
        .badge.badge-estado-desestimado {
            background-color: #7a6b9a !important;
            color: #f5f0fa !important;
            border: 1px solid #6a5b88;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => false, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h5 class="mb-0">Detalle de precompra #{{ $precompra->id }}</h5>
                        <a href="{{ route('admservicios.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al listado
                        </a>
                    </div>
                    <div id="detalle_precompra">
                        {!! $detalleHtml !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.ServerController = 'admservicios';
    </script>

    <script src="{{ versioned_asset('cajas/build/Admservicios.js') }}"></script>
@endpush
