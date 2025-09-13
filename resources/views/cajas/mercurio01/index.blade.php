<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

@php Tag::ModalGeneric(
    $title,
    View::render("mercurio01/tmp/form")
) @endphp

<script src="{{ asset('Cajas/build/Basicas.js') }}"></script>
