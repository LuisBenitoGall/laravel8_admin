@props(['class' => '', 'disabled' => false, 'id' => false, 'name', 'readonly' => false, 'required' => false, 'value' => '', 'wire' => false])

<div>
    <textarea 
        name="{{ $name }}"
        class="form-control {{ $class }}" 
        @if($id) id="{{ $id }}" @endif
        @if($disabled) disabled @endif
        @if($required) required @endif 
        @if($readonly) readonly @endif 
        @if($wire) wire:model.defer="{{ $name }}" @endif
    >{!! old($name, $value) !!}</textarea>

    <x-input-errors name="{{ $name }}"/>
</div>