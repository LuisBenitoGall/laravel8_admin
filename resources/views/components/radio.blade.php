@props(['checked' => false, 'class' => '', 'disabled' => false, 'id' => false, 'name', 'required' => false, 'style' => false, 'title' => false, 'value' => ''])

<div class="inline-block">
    <label class="custom-chk-rdb mr-4" 
        @if($style) style="{{ $style }}" @endif
    >
        <input type="radio" 
            name="{{ $name }}" 
            value="{{ $value }}" 
            @if($required) required @endif
            @if($id) id="{{ $id }}" @endif
            @if($checked) checked @endif
        >
        <span class="rdb chksmall {{ $class }}"></span>
        {!! ucfirst(trans('textos.'.$title)) !!}
    </label>    
</div>