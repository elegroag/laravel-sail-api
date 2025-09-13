@php
use App\Services\Tag;
@endphp
@php echo Tag::filtro($campo_filtro); @endphp

<!-- Modal Captura -->
@php echo Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio04/tmp/form",
        array('principal' => $Mercurio04->getPrincipalArray(), 'estados' => $Mercurio04->getEstadoArray())
    )
); @endphp

@php echo Tag::ModalCapture(
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
); @endphp

@php echo Tag::ModalCapture(
    array(
        'name' => 'ModalCapturarCiudades',
        'titulo' => 'Capturar ciudades',
        'contenido' => View::render(
            "mercurio04/tmp/capture_ciudades",
            array('ciudades' => $ciudades)
        )
    )
); @endphp


<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>


@php echo Tag::javascriptInclude('Cajas/oficinas/build.oficinas'); @endphp
