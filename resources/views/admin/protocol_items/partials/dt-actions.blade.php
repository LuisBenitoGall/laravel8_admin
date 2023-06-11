@can('protocols.edit')
	{{-- Estado --}}
	@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])

    {{-- Editar --}}
	<a href="{{ route('protocol-items.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar --}}
	<form method="POST" action="{{ route('protocol-items.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.item_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan