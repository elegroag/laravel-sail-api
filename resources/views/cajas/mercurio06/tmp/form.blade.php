 @php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
<div class="form-group">
    <label for="tipo" class="form-control-label">Tipo</label>
    @php echo Tag::textUpperField("tipo", "class: form-control", "placeholder: tipo"); @endphp
</div>
<div class="form-group">
    <label for="detalle" class="form-control-label">Detalle</label>
    @php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); @endphp
</div>
@php echo Tag::endform(); @endphp