@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'className' => 'form-control',
    'event' => null,
])
<label for="{{$name}}" class="control-label ml-0">{{$label}}</label>
<div class='input-group'>
    <div class='input-group-prepend'>
        @if (isset($event))
            <button 
                class='btn btn-sm btn-icon btn-primary' 
                type='button' 
                data-name='{{$name}}' 
                data-toggle="address">
                    <i class='fas fa-pen'></i>
            </button>
        @else
            <button 
                class='btn btn-sm btn-icon btn-primary' 
                type='button' 
                onclick="openAddress('{{$name}}')">
                    <i class='fas fa-pen'></i>
            </button>
        @endif
    </div>
    <input type='text' 
        name='{{$name}}' 
        id='{{$id ? $id : $name}}' 
        value='{{$value}}'
        placeholder="{{$placeholder}}" 
        class="{{isset($className) ? $className : 'form-control'}}"
        readonly 
    />
</div>