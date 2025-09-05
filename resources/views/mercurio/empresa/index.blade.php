@section('styles')
    @vite(['resources/js/Mercurio/vendors/datatables.net.bs5/css/dataTables.bootstrap5.min.css'])
@endsection

@section('scripts')
    @vite([
        'resources/js/Mercurio/vendors/datatables.net/js/dataTables.min.js',
        'resources/js/Mercurio/vendors/datatables.net.bs5/js/dataTables.bootstrap5.min.js'
    ])
@endsection

<div id='boneLayout'></div>

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

@vite(['resources/js/Mercurio/empresas/empresas.build.js'])
