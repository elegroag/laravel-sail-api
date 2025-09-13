<?php
echo View::getContent();
echo Tag::filtro($campo_filtro);
?>

<div id='boneLayout'></div>

<script type="text/template" id='tmp_layout'>
    <div id='consulta' class='table-responsive'></div>
    <div id='paginate' class='card-footer py-4'></div>
</script>

<?= Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio14/tmp/form",
        array(
            'tipopc' => $tipopc,
            'coddoc' => $coddoc,
            'tipsoc' => $tipsoc
        )
    ),
    'data-toggle="guardar"',
    'btnModalCapturarCampo',
    'modal_capturar_campo'
) ?>

<?= Tag::javascriptInclude('Cajas/docureqempresas/build.docureqempresas'); ?>
