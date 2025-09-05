@props([
    'name',
    'label',
    'value' => '',
    'placeholder' => '',
    'maxlength' => null,
    'minlength' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'class' => 'form-control',
    'attributes' => ''
])

<div class="form-group" group-for="{{ $name }}">
    <label for="{{ $name }}" class="control-label">{{ $label }}</label>
    {{ Tag::numericField(
        $name,
        "class: {$class}",
        "placeholder: {$placeholder}",
        $value ? "value: {$value}" : '',
        $maxlength ? "maxlength: {$maxlength}" : '',
        $minlength ? "minlength: {$minlength}" : '',
        "type: number",
        "event: is_numeric",
        $required ? 'required: true' : '',
        $readonly ? 'readonly: true' : '',
        $disabled ? 'disabled: true' : '',
        $attributes
    ) }}
</div>
