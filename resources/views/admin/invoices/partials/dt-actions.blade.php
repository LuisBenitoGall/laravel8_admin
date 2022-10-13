@can('invoices.edit')
	{{-- Estado --}}
	@if($manage_status)
		@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])
	@endif

    {{-- Editar --}}
	<a href="{{ route('invoices.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

{{-- Ver --}}
<a href="{{ route('invoices.show', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

{{-- Eliminar - condiciones: 
	1. Disponer de permisos.
	2. Sólo proveedores.
--}}
@can('invoices.destroy')
	@if($environment == 'sales')
		<form method="POST" action="{{ route('invoices.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		    <input name="_method" type="hidden" value="DELETE">
		    {{ csrf_field() }}
		    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.factura_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endif
@endcan