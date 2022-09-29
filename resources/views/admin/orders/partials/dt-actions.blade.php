@can('orders.edit')
	{{-- Estado --}}
	@if($manage_status)
		@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])
	@endif

    {{-- Editar --}}
	<a href="{{ route('orders.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('orders.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="{{ route('orders.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.pedido_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan