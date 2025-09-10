<script id='tmp_filtro' type="text/template">
    @php echo TagUser::filtro($campo_filtro, 'aplicar_filtro') @endphp
</script>

<script id='tmp_list_header' type="text/template">
    @include('templates.tmp_list_header')
</script>

<script type="text/template" id='tmp_layout'>
    @include('templates.tmp_layout')
</script>

<script type="text/template" id='tmp_header'>
    @include('templates.tmp_header')
</script>

<script type="text/template" id='tmp_rechazar'>
    @include('templates.tmp_rechazar')
</script>

<script type="text/template" id='tmp_devolver'>
    @include('templates.tmp_devolver')
</script>

<script type="text/template" id='tmp_info'>
    @include('templates.tmp_information')
</script>

<script type="text/template" id='tmp_aprobar'>
    @include('actualizardatos.tmp.tmp_aprobar')
</script>

<script id='tmp_aportes' type='text/template'>
    @include('templates.tmp_aportes')
</script>

<script type="text/template" id="tmp_reaprobar">
    @include('templates.tmp_reaprobar')
</script>

<script id='tmp_info_header' type="text/template">
    @include('templates.tmp_info_header')
</script>

<script type="text/template" id='tmp_deshacer'>
    @include('templates.tmp_deshacer')
</script>

<script id='tmp_info_header' type="text/template">
    @include('templates.tmp_info_header')
</script>

<script id='tmp_table' type="text/template">
    <div id='consulta' class='table-responsive'></div>
	<div id='paginate' class='card-footer py-4'></div>
	<div class='card-footer'>
		<div style='float:right'>
			<a class='btn btn-xs' id='btPendienteEmail' data-href="actualizardatos/pendiente_email">Procesar Notificación Pendiente</a>
		</div>
	</div>
	<div id='filtro'></div>
</script>

<div id='boneLayout'></div>

<script src="{{ asset('js/cajas/actualizardatos.js') }}"></script>