<?php
echo View::getContent();
echo Tag::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<?= Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio11/tmp/form"
    )
) ?>
<?= Tag::javascriptInclude('Cajas/motivorechazo/build.motivorechazo'); ?>
