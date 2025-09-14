@php

echo Tag::filtro($campo_filtro);
@endphp

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

@php echo Tag::ModalGeneric(
    $title,
    View::render("mercurio09/tmp/form")
) @endphp

@php echo Tag::ModalCapture(
    array(
        'name' => 'ModalCapturaArchivos',
        'titulo' => 'Requeridos por afiliados',
        'contenido' => View::render(
            "mercurio09/tmp/capture_archivos"
        )
    )
) @endphp

@php echo Tag::ModalCapture(
    array(
        'name' => 'ModalCapturaEmpresa',
        'titulo' => 'Requeridos por empresa',
        'contenido' => View::render(
            "mercurio09/tmp/capture_empresa",
            array('_tipsoc' => $_tipsoc)
        )
    )
) @endphp

@php echo Tag::javascriptInclude('Cajas/tipoopciones/build.tipoopciones'); @endphp
