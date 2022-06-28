@if($order->discount && $order->discount > 0)
	<label class="mr-5">
		{{ ucfirst(trans('textos.descuento_aplicado')) }}:
		<b>{{ $order->discount }}%</b>

		{{-- Eliminar descuento - condiciones: 
			1. Sólo el proveedor.
			2. Variable recibida = true;
		--}}
		@if($order->provider_id == session('currentCompany') && $remove_discount)
			<a href="{{ route('orders.remove-discount', [$order->id, $return]) }}" class="text-danger ml-3 btn-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.descuento_eliminar')) !!}" data-msg="{!! ucfirst(trans('textos.descuento_confirm_eliminar')) !!}">
				<i class="fa fa-trash-alt"></i>
			</a>
		@endif
	</label>
@endif