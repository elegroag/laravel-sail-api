@php
use App\Services\Tag;
@endphp

@php echo Tag::form("", "id: form", "class: validation_form", "autocomplete: off", "novalidate"); @endphp
<div class="form-group">
    <label for="codapl" class="form-control-label">Aplicativo</label>
    @php echo Tag::textUpperField("codapl", "class: form-control", "placeholder: Aplicativo"); @endphp
</div>
<div class="form-group">
    <label for="email" class="form-control-label">Email</label>
    @php echo Tag::textField("email", "class: form-control", "placeholder: Email "); @endphp
</div>
<div class="form-group">
    <label for="clave" class="form-control-label">Clave</label>
    @php echo Tag::textField("clave", "class: form-control", "placeholder: Clave"); @endphp
</div>
<div class="form-group">
    <label for="path" class="form-control-label">Path</label>
    @php echo Tag::textField("path", "class: form-control", "placeholder: Path"); @endphp
</div>
<div class="form-group">
    <label for="ftpserver" class="form-control-label">ftpserver</label>
    @php echo Tag::textField("ftpserver", "class: form-control", "placeholder: Path"); @endphp
</div>
<div class="form-group">
    <label for="pathserver" class="form-control-label">pathserver</label>
    @php echo Tag::textField("pathserver", "class: form-control", "placeholder: Path Server"); @endphp
</div>
<div class="form-group">
    <label for="userserver" class="form-control-label">userserver</label>
    @php echo Tag::textField("userserver", "class: form-control", "placeholder: userserver"); @endphp
</div>
<div class="form-group">
    <label for="passserver" class="form-control-label">passserver</label>
    @php echo Tag::textField("passserver", "class: form-control", "placeholder: passserver"); @endphp
</div>
@php echo Tag::endform(); ?>
