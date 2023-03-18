@can('orders.edit')
	{{-- Estado - condiciones: 
		1. Exclusivo proveedor o por cliente para facturas de proveedores sin cuenta en el ERP:
	--}}
	@if($order->provider_id == session('currentCompany') || ($order->customer_id == session('currentCompany') && !$provider_account))
		@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $item])
	@endif

	{{-- Ver --}}
    <a href="#" class="btn btn-xs btn-info btnSeeItem" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

    {{-- Editar - condiciones 
		1. Disponer de permisos.
		2. Exclusivo proveedor o para cliente con facturas de proveedores sin cuenta en el ERP:
		3. Facturas no bloqueadas.
    --}}
    {{-- 4/11/22: se omite la condición de pedido facturado. Se pueden añadir líneas incluso facturado el pedido. --}}
    {{-- @if(!$order->invoiced) --}}
    @can('invoices.edit')
    	@if($order->provider_id == session('currentCompany') || ($order->customer_id == session('currentCompany') && !$provider_account))
    		@if(!$invoice->blocked)
				<a href="#" class="btn btn-xs btn-info btnEditItem" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-edit-line" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
			@endif
		@endif
	@endcan
	{{-- @endif --}}

	{{-- Ronda de cambios - condiciones 
		1. Exclusivo para proveedores.
		2. Sólo accesible en pedidos.
	--}}
	@if($order->provider_id == session('currentCompany'))
		@if($environment == 'orders')
			<a href="#" class="btn btn-xs btn-info btnChangesRound" rel="tooltip" title="{!! ucfirst(trans('textos.ronda_cambios')) !!}" data-toggle="modal" data-target="#modal-changes-round" data-id="{{ $item->id }}" data-line="{{ $item->name }}">
				<i class="fa fa-tasks"></i>
			</a>
		@endif
	@endif

	{{-- Eliminar - condiciones:
		1. Disponer de permisos.
        2. No debe ser pedido vinculado a agrupación ni estar facturado.
        3. Para clientes con facturas de proveedores sin cuenta en el ERP:
	--}}
	@if( (!$order->invoiced && !$order->order_parent) || ($order->customer_id == session('currentCompany') && !$provider_account) )
		<form method="POST" action="{{ route('items.destroy', $item) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		    <input name="_method" type="hidden" value="DELETE">
		    {{ csrf_field() }}
		    <input type="hidden" name="environment" value="{{ $environment }}">
		    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.pedido_item_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endif
@endcan