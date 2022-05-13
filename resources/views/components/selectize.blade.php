@props(['class' => '', 'disabled' => false, 'id' => false, 'maxlength' => false, 'name', 'required' => false, 'style' => false, 'value' => '', 'warning' => false])

<div class="{{ $class }} selectize">
    <input type="text" 
        name="{{ $name }}" 
        class="form-control input-tags"
        @if($id) id="{{ $id }}" @endif 
        @if($maxlength) maxlength="{{ $maxlength }}" @endif 
        value="{{ $value }}"
    >

    @if($warning)
        <small class="text-warning"
            @if($style) style="{{ $style }}" @endif
        >{!! trans('textos.'.$warning) !!}</small>
    @endif

    <x-input-errors name="{{ $name }}"/>
</div>

@once
@push('scripts')
<script src="{{ asset('plugins/selectize/dist/js/standalone/selectize.js') }}"></script>
<script>
$(function(){
    $('.input-tags').selectize({
        delimiter: ',',
        persist: false,
        createOnBlur: true,
        create: true
    });
});
</script>
@endpush
@endonce
