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

        #chips_estado {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        #chips_estado .chip-estado {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #cfd8dc;
            background: #fff;
            color: #37474f;
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8125rem;
            font-weight: 500;
            line-height: 1.2;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
        }
        #chips_estado .chip-estado:hover {
            border-color: #26a69a;
            color: #00695c;
            background: #f1faf9;
        }
        #chips_estado .chip-estado.active {
            background: #00897b;
            border-color: #00897b;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 137, 123, 0.28);
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
                        <div>
                            <div class="small text-muted mb-2">Filtrar por estado</div>
                            <div id="chips_estado" role="group" aria-label="Filtro rápido por estado">
                                <button type="button" class="chip-estado active" data-toggle="chip-estado" data-estado="">Todos</button>
                                @foreach ($filtro_estados as $codigo => $descripcion)
                                    <button type="button" class="chip-estado" data-toggle="chip-estado" data-estado="{{ $codigo }}">{{ $descripcion }}</button>
                                @endforeach
                            </div>
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

@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    <script>
        window.ServerController = 'admservicios';
    </script>

    <script src="{{ versioned_asset('cajas/build/Admservicios.js') }}"></script>
@endpush
