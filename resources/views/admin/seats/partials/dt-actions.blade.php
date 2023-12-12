@can('seats.edit')
	{{-- Editar --}}
	<a href="{{ route('seats.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

{{-- Eliminar asiento --}}
@can('seats.destroy')
	<form method="POST" action="{{ route('seats.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-danger btn-xs frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.asiento_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.asiento_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan