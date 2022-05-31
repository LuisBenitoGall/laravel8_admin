@props(['stars' => 5, ])

<div class="rating">
    @php
    $i = 1;
    @endphp

    @while($i <= $stars)
        <i class="fa fa-star @if($i <= $points) active @endif" data-star="{{ $i }}"></i>

        @php
        $i++;
        @endphp
    @endwhile

    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $points }}">
</div>

@once
@push('scripts')
<script>
$(function(){
    var stars = {{ $stars }};
    var name = '{{ $name }}';

    //Establecer puntuación.
    $('.rating .fa').on('click', function(){
        var points = $(this).data('star');
        $('#'+name).val(points);

        $('.rating .fa').each(function(){
            var point = $(this).data('star');
            if(point <= points){
                $(this).addClass('active');
            }else{
                $(this).removeClass('active');    
            }
        });
    });
});
</script>
@endpush
@endonce