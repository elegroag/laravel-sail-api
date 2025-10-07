@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')
    @include('cajas/templates/tmp_filtro', [
        'campo_filtro' => $campo_filtro
    ])

    <div class="card border-0 m-2">
        <div class="card-header">
            <h4 class="font-weight-bold">{{ $title }}</h4>
        </div>
        <div id='consulta' class='table-responsive'></div>
        <div id='paginate' class='card-footer py-4'></div>
    </div>
@endsection

@push('scripts')
    @include("partials.modal_generic", [
        "titulo" => 'Configuración básica',
        "contenido" => '',
        "evento" => 'data-toggle="guardar"',
        "btnShowModal" => 'btCaptureModal',
        "idModal" => 'captureModal']
    )

    <script>
        window.ServerController = 'mercurio03';
    </script>

    <script src="{{ asset('cajas/build/Firmas.js') }}"></script>
@endpush
