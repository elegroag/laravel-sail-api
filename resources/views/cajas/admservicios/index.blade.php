@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
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
@endsection

@push('scripts')
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])

    <script>
        window.ServerController = 'admservicios';
    </script>

    <script src="{{ asset('cajas/build/Admservicios.js') }}"></script>
@endpush
