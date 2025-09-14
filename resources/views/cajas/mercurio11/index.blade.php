@php
use App\Services\Tag;

echo Tag::filtro($campo_filtro);
@endphp

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

@php echo Tag::ModalGeneric(
    $title,
    View::render(
        "mercurio11/tmp/form"
    )
) @endphp
<script src="{{ asset('Cajas/build/Motivorechazo.js') }}"></script>
