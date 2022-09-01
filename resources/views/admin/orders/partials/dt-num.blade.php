@php
$num = $row->order_num? $row->order_num:$row->id;
@endphp

<a href="{{ route('orders.edit', $row) }}">{{ $num }}</a>
