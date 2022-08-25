@props(['class' => '', 'disabled' => false, 'id' => false, 'lw' => false, 'name', 'readonly' => false, 'required' => false, 'value' => ''])

<div>
    <textarea 
        name="{{ $name }}"
        class="form-control {{ $class }}" 
        @if($lw) wire:model.defer="{{ $lw }}" @endif
        @if($id) id="{{ $id }}" @endif
        @if($disabled) disabled @endif
        @if($required) required @endif 
        @if($readonly) readonly @endif 
    >{!! old($name, $value) !!}</textarea>

    <x-input-errors name="{{ $name }}"/>
</div>