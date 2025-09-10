<?php
echo View::getContent();
echo TagUser::filtro($campo_filtro);
?>

<div id='boneLayout'></div>

<script type="text/template" id='tmp_layout'>
    <div id='consulta' class='table-responsive'></div>
    <div id='paginate' class='card-footer py-4'></div>
</script>

<?= TagUser::ModalGeneric(
    $title,
    View::render(
        "mercurio13/tmp/form",
        array(
            'tipopc' => $tipopc,
            'coddoc' => $coddoc
        )
    ),
    'data-toggle="guardar"',
    'btnModalCapturarCampo',
    'modal_capturar_campo'
) ?>

<?= Tag::javascriptInclude('Cajas/docureqtrabajadores/build.docureqtrabajadores'); ?>