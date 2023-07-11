@can('work-orders.edit')
	{{-- Estado --}}
	@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])

	{{-- Editar --}}
	<a href="#" class="btn btn-xs btn-info btnEditWorkOrderProduct" data-toggle="modal" data-target="#modal-edit-product" data-id="{{ $row->id }}" data-product="{{ $row->name }}" data-plannedqty="{{ $row->planned_qty }}" data-producedqty="{{ $row->produced_qty }}" data-observations="{{ $row->observations }}" data-batch="{{ $row->with_batch }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar - condiciones 
		1. Si el producto está en espera (1).
	--}}
	@if($row->status == 1)
		<form method="POST" action="{{ route('work-order-products.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	        <input name="_method" type="hidden" value="DELETE">
	        {{ csrf_field() }}
	        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.producto_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.producto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	    </form>
	@endif
@endcan