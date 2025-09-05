@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.min.css') }}">
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection

@section('scripts')
    <script type="text/template" id='tmp_layout'>
        @include('templates/tmp_layout')
    </script>

    <script type="text/template" id='tmp_subheader'>
        @include('templates/tmp_subheader')
    </script>

    <script type="text/template" id='tmp_card_header'>
        @include('templates/tmp_card_header')
    </script>

    <script type="text/template" id='tmp_table'>
        @include('independiente/tmp/tmp_table')
    </script>

    <script type="text/template" id='tmp_create'>
        @include('independiente/tmp/tmp_create')
    </script>

    <script type="text/template" id="tmp_seguimientos">
        @include('templates/tmp_seguimiento')
    </script>

    <script type="text/template" id="tmp_documentos">
        @include('templates/tmp_documentos')
    </script>

    <script type="text/template" id="tmp_docurow">
        @include('templates/tmp_docurow')
    </script>

    <script type="text/template" id='tmp_firmas'>
        @include('templates/tmp_firmas')
    </script>

    <script type="text/template" id='tmp_create_firma'>
        @include('templates/tmp_create_firma')
    </script>

    <script>
        const _TITULO = "{{ $title }}";
    </script>

    <script src="{{ asset('mercurio/Independientes.js') }}"></script>

@endsection
