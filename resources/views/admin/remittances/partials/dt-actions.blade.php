@can('remittances.edit')
{{-- Estado --}}
	@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])

{{-- Editar --}}
	<a href="{{ route('remittances.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('remittances.destroy')
	{{-- Eliminar: sólo si el estado es de pendiente --}}
	@if($row->status == config('constants.EFFECT_STATUS_PENDENT_'))
		<form method="POST" action="{{ route('remittances.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.remesa_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endif
@endcan
