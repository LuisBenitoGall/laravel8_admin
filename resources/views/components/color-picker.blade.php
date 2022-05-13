@props(['class' => '', 'disabled' => false, 'id' => false, 'info' => false, 'name', 'required' => false, 'readonly' => false, 'value' => ''])

<div>
    <input type="text" 
        name="{{ $name }}"
        id="{{ $id }}"
        class="form-control colorSelect {{ $class }}" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        value="{{ old($name, $value) }}" 
    >

    @if($info)
        {!! App\Helpers\Helpers::info($info) !!}
    @endif

    <x-input-errors name="{{ $name }}"/>
</div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script>
$(function(){
    //Colorpicker:
    $('.colorSelect').colorpicker({
        format: 'RGB'
    });

    //Cambio color dinámico:
    $('.colorSelect').on('change', function(){
        var color = $(this).val();
        var parent = $(this).closest('.input-group');
        parent.find('.input-group-addon').css('background-color', color);
    });
});

$(document).ready(function(){
    /*$(document).on('change', '#{{ $id }}', function(e){
        $this.set('{{ $name }}', e.target.value);
    });*/
});
</script>
@endpush
@endonce