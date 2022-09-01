<span class="xoption-{{ $row->id }}">
    @if(isset($estados[$row->order_status]))
        {!! ucfirst($estados[$row->order_status]) !!}
    @endif
</span>
               