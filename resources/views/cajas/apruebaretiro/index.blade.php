@extends('layouts.cajas-request')

@push('scripts')
    <script id='tmp_filtro' type="text/template">
        @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])
    </script>

    <script src="{{ asset('cajas/build/ApruebaRetiro.js') }}"></script>
@endpush
