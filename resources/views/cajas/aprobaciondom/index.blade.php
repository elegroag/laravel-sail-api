
<script id='tmp_filtro' type="text/template">
    <?= TagUser::filtro($campo_filtro, 'aplicar_filtro') ?>
</script>

<script id='tmp_list_header' type="text/template">
    <?= View::renderView("templates/tmp_list_header"); ?>
</script>

<script type="text/template" id='tmp_layout'>
    <?= View::renderView("templates/tmp_layout"); ?>
</script>

<script type="text/template" id='tmp_header'>
    <?= View::renderView("templates/tmp_header"); ?>
</script>

<script type="text/template" id='tmp_rechazar'>
    <?= View::renderView("templates/tmp_rechazar"); ?>
</script>

<script type="text/template" id='tmp_devolver'>
    <?= View::renderView("templates/tmp_devolver"); ?>
</script>

<script id='tmp_aportes' type='text/template'>
    <?= View::renderView("templates/tmp_aportes"); ?>
</script>

<script type="text/template" id='tmp_info'>
    <?= View::renderView("templates/tmp_information"); ?>
</script>

<script type="text/template" id='tmp_aprobar'>
    <?= View::renderView("aprobaciondom/tmp/tmp_aprobar"); ?>
</script>

<script id='tmp_info_header' type="text/template">
	<?= View::renderView("templates/tmp_info_header"); ?>
</script>

<script type="text/template" id="tmp_reaprobar">
    <?= View::renderView("templates/tmp_reaprobar"); ?>
</script>

<script id='tmp_table' type="text/template">
    <div id='consulta' class='table-responsive'></div>
	<div id='paginate' class='card-footer py-4'></div>
	<div class='card-footer'>
	</div>
	<div id='filtro'></div>
</script>

<div id='boneLayout'></div>
<?= Tag::javascriptInclude('Cajas/serviciodomesticos/build.serviciodomesticos'); ?>