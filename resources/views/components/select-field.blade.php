@props([
    'name',
    'id',
    'label',
    'value' => '',
    'placeholder' => '',
    'maxlength' => null,
    'minlength' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'className' => 'form-control',
    'attributes' => '',
    'options' => []
])

<div class="form-group" group-for="{{ $name }}">
    <label for="{{ $id ? $id : $name }}" class="control-label">{{ $label }}</label>
    <select 
        name="{{ $name }}" 
        id="{{ $id ? $id : $name }}" 
        class="form-control {{ $className }}" 
        {{ $required ? 'required' : '' }} 
        {{ $disabled ? 'disabled' : '' }} 
        {{ $readonly ? 'readonly' : '' }} 
        {{ $attributes }}>
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>
</div>
