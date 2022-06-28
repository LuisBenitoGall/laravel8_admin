@if($order->is_order)
	<label class="mr-5">
		{{ ucfirst(trans('textos.pedido')) }}:	
		<b>
			<a href="{{ route('orders.edit', $order) }}" class="text-warning">
				@if($order->order_num) {{ $order->order_num }} @else {{ $order->id }} @endif
			</a>
		</b>
	</label>
@endif