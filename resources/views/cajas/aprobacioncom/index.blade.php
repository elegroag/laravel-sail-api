@extends('layouts.cajas-request')


@push('scripts')
    <script id='tmp_filtro' type="text/template">
        @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])
    </script>

    <script type="text/template" id='tmp_aprobar'>
        @include('cajas/aprobacioncom/tmp/tmp_aprobar')
    </script>

    <script src="{{ asset('cajas/build/MadresComunitarias.js') }}"></script>
@endpush
