@props(['class' => '', 'colors' => [], 'disabled' => false, 'id' => false, 'info' => false, 'name', 'required' => false, 'readonly' => false, 'value' => ''])

<div class="color-select {{ $class }}">
    <div class="color-grid">
        @foreach($colors as $c)
            @php
            list($rgb, $color_name) = explode('-', $c);
            @endphp
            @if(isset($rgb) && isset($color_name))
                <div class="color-cell" data-color="{{ strtolower($color_name) }}" style="background-color: {{ $rgb }};" rel="tooltip" title="{{ strtolower($color_name) }}">
                    @if(strtolower($value) == strtolower($color_name))
                        <i class="fa fa-check"></i>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <input type="hidden" name="{{ $name }}" id="color-selected" value="{{ $value }}">
</div>

@once
@push('scripts')
<script>
$(function(){
    $('.color-cell').on('click', function(){
        $('.color-cell').each(function (){
            $(this).empty();
        });
        var color = $(this).data('color');
        $(this).html('<i class="fa fa-check"></i>');

        $('#color-selected').val(color);
    });
});
</script>
@endpush
@endonce

