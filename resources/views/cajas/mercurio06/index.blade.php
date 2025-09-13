@php
use App\Services\Tag;
echo Tag::filtro($campo_filtro);
@endphp

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
@php echo Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio06/tmp/form"
    )
); @endphp

<!-- Modal Captura -->
@php echo Tag::ModalCapture(
    array(
        'name' => 'ModalCapturarCampo',
        'titulo' => 'Capturar campo',
        'contenido' => View::render("mercurio06/tmp/capture_campo")
    )
); @endphp

<?= Tag::javascriptInclude('Cajas/tipoacceso/build.tipoacceso'); ?>
