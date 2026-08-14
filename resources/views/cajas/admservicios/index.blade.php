@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <style>
        /* Desestimado (DE): purple pastel oscuro — no depende de bg-secondary del tema */
        .badge.badge-estado-desestimado {
            background-color: #7a6b9a !important;
            color: #f5f0fa !important;
            border: 1px solid #6a5b88;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')

@include('cajas/templates/tmp_header_adapter', ['sub_title' => $title, 'filtrar' => true, 'listar' => false, 'salir' => false, 'add' => false])
<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-green-blue p-1"></div>
                <div class="card-body p-0 m-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Filtro rápido por estado" id="chips_estado">
                            <button type="button" class="btn btn-outline-primary active" data-toggle="chip-estado" data-estado="">Todos</button>
                            @foreach ($filtro_estados as $codigo => $descripcion)
                                <button type="button" class="btn btn-outline-primary" data-toggle="chip-estado" data-estado="{{ $codigo }}">{{ $descripcion }}</button>
                            @endforeach
                        </div>
                        <a href="{{ route('admservicios.reporte', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-success" data-toggle="reporte-csv">
                            <i class="fas fa-file-csv me-1"></i> Exportar CSV
                        </a>
                    </div>
                    <input type="hidden" id="chip_estado" value="">
                    <div id='consulta' class='table-responsive'></div>
                    <div id='paginate' class='card-footer py-4'></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal detalle precompra + transacciones ePayco --}}
<div class="modal fade" id="modal_detalle_precompra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_detalle_precompra_titulo">Detalle de precompra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modal_detalle_precompra_body">
                <div class="text-center text-muted py-4">Cargando…</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    <script>
        window.ServerController = 'admservicios';
    </script>

    <script src="{{ versioned_asset('cajas/build/Admservicios.js') }}"></script>
@endpush
