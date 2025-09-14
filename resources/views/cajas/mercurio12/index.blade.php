@php
echo Tag::filtro($campo_filtro);
@endphp

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

@php echo Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio12/tmp/form"
    )
) @endphp

<script src="{{ asset('Cajas/build/Documentos.js') }}"></script>
