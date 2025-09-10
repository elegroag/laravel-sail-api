<?php
echo View::getContent();
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
<?= TagUser::ModalGeneric(
    $title,
    View::render("mercurio02/tmp/form", array('ciudades' => $ciudades))
) ?>

<?= Tag::javascriptInclude('Cajas/datoscaja/build.datoscaja'); ?>