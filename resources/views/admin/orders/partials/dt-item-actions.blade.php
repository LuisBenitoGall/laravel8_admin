@can('orders.edit')
	{{-- Estado --}}
	@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $item])

	{{-- Ver --}}
    <a href="#" class="btn btn-xs btn-info btnSeeItem" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

    {{-- Editar - condiciones 
		1. Disponer de permisos.
		2. No facturado.
    --}}
    @if(!$order->invoiced)
		<a href="#" class="btn btn-xs btn-info btnEditItem" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-edit-line" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
	@endif

	{{-- Eliminar - condiciones
		1. Disponer de permisos.
        2. No debe ser pedido vinculado a agrupación ni estar facturado.
	--}}
	@if(!$order->invoiced && !$order->order_parent)
		<form method="POST" action="{{ route('items.destroy', $item) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		    <input name="_method" type="hidden" value="DELETE">
		    {{ csrf_field() }}
		    <input type="hidden" name="environment" value="{{ $environment }}">
		    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.pedido_item_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endif
@endcan