<?php
echo View::getContent();
echo Tag::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>


<!-- Modal Captura -->
<?= Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio12/tmp/form"
    )
) ?>

<?= Tag::javascriptInclude('Cajas/documentos/build.documentos'); ?>
