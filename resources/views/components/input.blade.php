@props(['autocomplete' => false,'class', 'autofocus' => false , 'datas' => false, 'disabled' => false, 'group' => false, 'groupvalue' => '', 'id' => false, 'info' => false, 'lw' => false, 'max' => false, 'maxlength' => false, 'min' => false, 'name' => '',  'placeholder' => false, 'readonly' => false, 'required' => false, 'type' => '', 'value' => '', 'warning' => false, 'wire' => false])

<div>
    @if($group)
        <div class="input-group">
            @endif

            @if($group && $group == 'left')
                <div class="input-group-prepend">
                    <span class="input-group-addon">{{ $groupvalue }}</span>
                </div>
            @endif

            <input type="{{ $type }}"
                   name="{{ $name }}"
                   class="form-control {{ $class }}"
                   @if($id) id="{{ $id }}" @endif
                   @if($disabled) disabled @endif
                   @if($required) required @endif
                   @if($readonly) readonly @endif
                   @if($autofocus) autofocus @endif
                   @if($placeholder) placeholder="{{ $placeholder }}" @endif
                   value="{{ old($name, $value) }}"
                   @if($maxlength) maxlength="{{ $maxlength }}" @endif
                   @if($min) min="{{ $min }}" @endif
                   @if($max) max="{{ $max }}" @endif
                   @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                   @if($datas)
                       {!! $datas !!}
                   @endif
                   @if($wire) wire:model.defer="{{ $name }}" @endif
            >

            @if($group && $group == 'right')
                <div class="input-group-append">
                    <span class="input-group-addon">{{ $groupvalue }}</span>
                </div>
            @endif

            @if($group)
        </div>
    @endif

    @if($warning)
        <small class="text-warning">{!! trans('textos.'.$warning) !!}</small>
    @endif

    @if($info)
        {!! App\Helpers\helpers::info($info) !!}
    @endif

    <x-input-errors name="{{ $name }}"/>
</div>
