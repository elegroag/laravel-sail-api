<?= View::getContent() ?>

<script id='tmp_filtro' type="text/template">
    <?= TagUser::filtro($campo_filtro, 'aplicar_filtro') ?>
</script>

<script type="text/template" id='tmp_layout'>
    <?= View::renderView("usuario/tmp/tmp_layout"); ?>
</script>

<script type="text/template" id='tmp_header'>
    <?= View::renderView("templates/tmp_header"); ?>
</script>

<script type="text/template" id='tmp_tabla_usuarios'>
    <div  class='table-excel-wrapper' id='consulta'></div>
    <div class='mt-3' id='paginate'></div>
    <div><p>Total de registros: <span id='total_registros'></span> consultados.</p></div>
    <div id='filtro'></div>
</script>

<script type="text/template" id='tmp_detalle'>
    <?= View::renderView("usuario/tmp/tmp_detalle"); ?>
</script>

<script id='tmp_list_header' type="text/template">
    <div class="col">
        <div id="botones" class='d-flex justify-content-end'>
        <button type='button' data-tipo="" data-toggle='link' class='btn btn-sm btn-default'>Todos</button>&nbsp;    
        <button type='button' data-tipo="P" data-toggle='link' class='btn btn-sm btn-default'>Particulares</button>&nbsp;
            <button type='button' data-tipo="E" data-toggle='link' class='btn btn-sm btn-default'>Empresas</button>&nbsp;
            <button type='button' data-tipo="T" data-toggle='link' class='btn btn-sm btn-default'>Trabajadores</button>&nbsp;
            <button type='button' data-tipo="I" data-toggle='link' class='btn btn-sm btn-default'>Independientes</button>&nbsp;
            <button type='button' data-tipo="O" data-toggle='link' class='btn btn-sm btn-default'>Pensionados</button>&nbsp;
        </div>
    </div>
</script>

<div id='boneLayout'></div>

<?= Tag::javascriptInclude('Cajas/usuario/build.usuario'); ?>