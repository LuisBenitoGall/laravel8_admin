@php
$num = $order->order_num? $order->order_num:$order->id;
@endphp

<a href="{{ route('orders.edit', $order) }}">{{ $num }}</a>
