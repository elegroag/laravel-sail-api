@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div id='boneLayout'></div>
@endsection

@push('scripts')
<script src="{{ asset('assets/datatables.net/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<script type="text/template" id='tmp_layout'>
    @include('templates.tmp_layout')
</script>

<script type="text/template" id='tmp_subheader'>
    @include('templates.tmp_subheader')
</script>

<script type="text/template" id='tmp_card_header'>
    @include('templates.tmp_card_header')
</script>

<script type="text/template" id="tmp_seguimientos">
    @include('templates.tmp_seguimiento')
</script>

<script type="text/template" id="tmp_documentos">
    @include('templates.tmp_documentos')
</script>

<script type="text/template" id="tmp_docurow">
    <?= View::render("templates/tmp_docurow"); ?>
</script>

<script type="text/template" id='tmp_firmas'>
    @include('templates.tmp_firmas')
</script>

<script type="text/template" id='tmp_create_firma'>
    @include('templates.tmp_create_firma')
</script>

<script type="text/template" id='tmp_table'>
    @include('empresa.tmp.tmp_table')
</script>

<script type="text/template" id='tmp_create'>
    @include('empresa.tmp.tmp_create')
</script>

<script type="text/template" id='tmp_tranom'>
    @include('empresa.tmp.tmp_tranom')
</script>

<script src="{{ asset('mercurio/Empresas.js') }}"></script>

@endpush
