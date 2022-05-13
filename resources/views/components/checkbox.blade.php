@props(['checked' => false, 'class' => '', 'datas' => false, 'disabled' => false, 'id' => false, 'inputclass' => false, 'name' => '', 'required' => false, 'style' => false, 'title' => false, 'value' => ''])

<div class="inline-block">
    <label class="custom-chk-rdb" 
        @if($title) rel="tooltip" title="{!! trans('textos.'.$title) !!}" @endif
    >
        <input type="checkbox" 
            name="{{ $name }}" 
            value="{{ $value }}"
            @if($inputclass) class="{{ $inputclass }}" @endif 
            @if($required) required @endif
            @if($id) id="{{ $id }}" @endif
            @if($checked) checked @endif
            @if($datas) {!! $datas !!} @endif
        >
        <span class="chk {{ $class }}" 
            @if($style) style="{{ $style }}" @endif
        ></span>
    </label>    
</div>