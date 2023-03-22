<span class="xoption-{{ $row->id }}">
    @if(isset($estados[$row->status]))
        {!! ucfirst($estados[$row->status]) !!}
    @endif
</span>
               