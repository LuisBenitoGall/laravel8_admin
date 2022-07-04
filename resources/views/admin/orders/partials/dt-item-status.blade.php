<span class="xoption-{{ $item->id }}">
    @if(isset($estados[$item->status]))
        {!! ucfirst($estados[$item->status]) !!}
    @endif
</span>
               