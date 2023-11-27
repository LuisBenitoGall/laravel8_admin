@can('seats.edit')
	{{-- Editar --}}
	<a href="{{ route('seat-models.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('seats.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="seat-models/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.modelo_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.modelo_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan