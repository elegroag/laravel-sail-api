@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    @include('mercurio/templates/tmp_clave_firma')

    <script type="text/template" id='tmp_layout'>
        @include('mercurio/templates/tmp_layout')
    </script>

    <script type="text/template" id='tmp_subheader'>
        @include('mercurio/templates/tmp_subheader')
    </script>

    <script type="text/template" id='tmp_card_header'>
        @include('mercurio/templates/tmp_card_header')
    </script>

    <script type="text/template" id='tmp_table'>
        @include('mercurio/independiente/tmp/tmp_table')
    </script>

    <script type="text/template" id='tmp_create'>
        @include('mercurio/independiente/tmp/tmp_create')
    </script>

    <script type="text/template" id="tmp_seguimientos">
        @include('mercurio/templates/tmp_seguimiento')
    </script>

    <script type="text/template" id="tmp_documentos">
        @include('mercurio/templates/tmp_documentos')
    </script>

    <script type="text/template" id="tmp_docurow">
        @include('mercurio/templates/tmp_docurow')
    </script>

    <script type="text/template" id='tmp_firmas'>
        @include('mercurio/templates/tmp_firmas')
    </script>

    <script type="text/template" id='tmp_create_firma'>
        @include('mercurio/templates/tmp_create_firma')
    </script>

    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'independiente';
    </script>

    <script src="{{ asset('mercurio/build/Independientes.js') }}"></script>

    @endpush
