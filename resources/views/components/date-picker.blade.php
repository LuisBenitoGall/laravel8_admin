@props(['class' => '', 'disabled' => false, 'format', 'id' => false, 'name', 'readonly' => false, 'required' => false, 'value' => '', 'warning' => false, 'info' => false])

<div class="input-group date">
    <span class="input-group-addon">
        <i class="fa fa-calendar"></i>
    </span>
    <input type="text" 
        name="{{ $name }}" 
        class="form-control {{ $class }}" 
        @if($id) id="{{ $id }}" @endif
        @if($disabled) disabled @endif
        @if($required) required @endif
        @if($readonly) readonly @endif 
        value="{{ $value }}"
    >    

    @if($warning)
        <small class="text-warning">{!! trans('textos.'.$warning) !!}</small>
    @endif

    @if($info)
        {!! App\Helpers\helpers::info($info) !!}
    @endif

    <x-input-errors name="{{ $name }}"/>
</div>

@once
@push('scripts')
<script>
$(function(){
    $('.date').datepicker({
        autoclose: true,
        calendarWeeks: true,
        // clearBtn: true,
        //format: '{{ $format }}',
        format: 'd/m/Y',
        keyboardNavigation: false,
        language: '{{ config('app.locale') }}',
        todayBtn: true,
        todayHighlight: true
    });
});
</script>
@endpush
@endonce