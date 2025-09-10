<?php
echo View::getContent();
echo TagUser::filtro($campo_filtro);
?>

<!-- Modal Captura -->
<?= TagUser::ModalGeneric(
    $title,
    View::render(
        "mercurio04/tmp/form",
        array('principal' => $Mercurio04->getPrincipalArray(), 'estados' => $Mercurio04->getEstadoArray())
    )
) ?>

<?= TagUser::ModalCapture(
    array(
        'name' => 'ModalCapturarOpciones',
        'titulo' => 'Capturar opciones',
        'contenido' => View::render(
            "mercurio04/tmp/capture_opciones",
            array(
                'mercurio09' => $Mercurio09->find(),
                'gener02' => $Gener02->find()
            )
        )
    )
) ?>

<?= TagUser::ModalCapture(
    array(
        'name' => 'ModalCapturarCiudades',
        'titulo' => 'Capturar ciudades',
        'contenido' => View::render(
            "mercurio04/tmp/capture_ciudades",
            array('ciudades' => $ciudades)
        )
    )
) ?>


<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>


<?= Tag::javascriptInclude('Cajas/oficinas/build.oficinas'); ?>