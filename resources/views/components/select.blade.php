@props(['class' => '', 'disabled' => false, 'id' => false, 'info' => false, 'label' => false, 'multiple' => false, 'name', 'options', 'readonly' => false, 'required' => false, 'value' => '', 'values' => [], 'warning' => false, 'wire' => false])

<div>
    {{-- <div wire:ignore> --}}
    <div>
        <select name="{{ $name }}" 
            class="form-control {{ $class }}" 
            @if($id) id="{{ $id }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($multiple) multiple @endif 
            @if($wire) wire:model.defer="{{ $name }}" @endif
        >
            @if($label)
                <option value="" @if(empty($values)) selected @endif disabled>{!! ucfirst(trans('textos.'.$label)) !!}</option>
            @endif

            @foreach($options as $idx => $val)
                @if($multiple)
                    @php
                    $selected = !empty($values) && in_array($idx, $values)? 'selected':'';
                    @endphp
                @else
                    @php
                    $selected = $idx == $value? 'selected':'';
                    @endphp
                @endif

                <option value="{{ $idx }}" {{ $selected }}>{{ ucfirst($val) }}</option>    
            @endforeach
        </select>
    </div>

    @if($info)
        {!! App\Helpers\helpers::info($info) !!}
    @endif

    <x-input-errors name="{{ $name }}"/>
</div>
