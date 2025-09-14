 @php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
<div class="form-group">
    <label for="tipopc" class="form-control-label">Tipo</label>
    @php echo Tag::textUpperField("tipopc", "class: form-control", "placeholder: Tipo"); @endphp
</div>
<div class="form-group">
    <label for="detalle" class="form-control-label">Detalle</label>
    @php echo Tag::textUpperField("detalle", "class: form-control", "placeholder: Detalle"); @endphp
</div>
<div class="form-group">
    <label for="dias" class="form-control-label">Dias</label>
    @php echo Tag::numericField("dias", "class: form-control", "placeholder: Dias"); @endphp
</div>
@php echo Tag::endform(); @endphp