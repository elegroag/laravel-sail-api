@php
use App\Services\Tag;
@endphp

@php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
<div class="form-group">
    <label for="coddoc" class="form-control-label">Documento</label>
    @php echo Tag::textUpperField("coddoc", "class: form-control", "placeholder: Documento"); @endphp
</div>
<div class="form-group">
    <label for="detalle" class="form-control-label">Detalle</label>
    @php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); @endphp
</div>
@php echo Tag::endform(); @endphp
