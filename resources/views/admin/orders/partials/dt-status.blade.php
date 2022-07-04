<span class="xoption-{{ $order->id }}">
    @if(isset($estados[$order->order_status]))
        {!! ucfirst($estados[$order->order_status]) !!}
    @endif
</span>
               