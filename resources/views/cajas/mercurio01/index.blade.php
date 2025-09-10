<?php
echo View::getContent();
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<?= TagUser::ModalGeneric(
    $title,
    View::render("mercurio01/tmp/form")
) ?>

<?= Tag::javascriptInclude('Cajas/basicas/build.basicas'); ?>