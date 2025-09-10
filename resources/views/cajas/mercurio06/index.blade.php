<?php
echo View::getContent();
echo TagUser::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>


<!-- Modal Captura -->
<?= TagUser::ModalGeneric(
    $title,
    View::render(
        "mercurio06/tmp/form"
    )
) ?>

<!-- Modal Captura -->
<?= TagUser::ModalCapture(
    array(
        'name' => 'ModalCapturarCampo',
        'titulo' => 'Capturar campo',
        'contenido' => View::render("mercurio06/tmp/capture_campo")
    )
) ?>

<?= Tag::javascriptInclude('Cajas/tipoacceso/build.tipoacceso'); ?>