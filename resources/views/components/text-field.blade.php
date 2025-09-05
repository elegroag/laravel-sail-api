@props([
    'name',
    'label',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'class' => 'form-control',
    'attributes' => ''
])

<div class="form-group" group-for="{{ $name }}">
    <label for="{{ $name }}" class="control-label">{{ $label }}</label>
    {{ Tag::textUpperField(
        $name,
        "class: {$class}",
        "placeholder: {$placeholder}",
        $value ? "value: {$value}" : '',
        $required ? 'required: true' : '',
        $readonly ? 'readonly: true' : '',
        $disabled ? 'disabled: true' : '',
        $attributes
    ) }}
</div>
