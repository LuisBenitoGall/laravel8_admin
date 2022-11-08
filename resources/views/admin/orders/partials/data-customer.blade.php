<label class="mr-5">
	{!! ucfirst(trans('textos.cliente')) !!}: 
	<b>
		{{ ucfirst($customer->name) }}
	</b>

	{{-- Modificar cliente - condiciones:
		1. No existe pedido y empresa en sesión es el proveedor.
		2. No existen facturas y empresa en sesión es el proveedor.
		3. El pedido no está facturado.
		...
	--}}
	@if( (!$order && $order->provider_id == session('currentCompany') ) || ( !$invoices->count() && $order->provider_id == session('currentCompany')) && !$order->invoiced)
		<a href="#" class="ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.cliente_modificar')) }}" data-toggle="modal" data-target="#modal-set-customer">
			<i class="fa fa-edit"></i>
		</a>
	@endif
</label>