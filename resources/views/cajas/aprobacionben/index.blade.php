@extends('layouts.cajas-request')

@push('scripts')
    <script id='tmp_filtro' type="text/template">
        @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])
    </script>


    <script type="text/template" id='tmp_aprobar'>
        @include('cajas/aprobacionben/tmp/tmp_aprobar')
    </script>


    <script id='tmp_conyuge' type='text/template'>
    </script>


    <script id='tmp_pendiente_mail' type="text/template">
    </script>

    <script src="{{ asset('cajas/build/Beneficiarios.js') }}"></script>
@endpush
