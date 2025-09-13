<?php
echo View::getContent();
echo Tag::filtro($campo_filtro);
?>

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<?= Tag::ModalGeneric(
    $title,
    View::render("mercurio09/tmp/form")
) ?>

<?= Tag::ModalCapture(
    array(
        'name' => 'ModalCapturaArchivos',
        'titulo' => 'Requeridos por afiliados',
        'contenido' => View::render(
            "mercurio09/tmp/capture_archivos"
        )
    )
) ?>

<?= Tag::ModalCapture(
    array(
        'name' => 'ModalCapturaEmpresa',
        'titulo' => 'Requeridos por empresa',
        'contenido' => View::render(
            "mercurio09/tmp/capture_empresa",
            array('_tipsoc' => $_tipsoc)
        )
    )
) ?>

<?= Tag::javascriptInclude('Cajas/tipoopciones/build.tipoopciones'); ?>
