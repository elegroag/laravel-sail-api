@php
use App\Services\Tag;

echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
<div class="form-group">
    <label for="codest" class="form-control-label">Codigo</label>
    @php echo Tag::textUpperField("codest", "class: form-control", "placeholder: Codigo"); @endphp
</div>
<div class="form-group">
    <label for="detalle" class="form-control-label">Detalle</label>
    @php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); @endphp
</div>
@php echo Tag::endform(); @endphp
