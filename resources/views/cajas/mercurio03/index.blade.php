<?php
echo View::getContent();
echo Tag::addJavascript('core/upload');
echo TagUser::filtro($campo_filtro);
?>

<?= TagUser::ModalGeneric(
    $title,
    View::render("mercurio03/tmp/form")
) ?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<?= Tag::javascriptInclude('Cajas/firmas/build.firmas'); ?>