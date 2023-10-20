@can('accounting-account-types.edit')
	{{-- Editar --}}
	<a href="#" class="btn btn-xs btn-info btnEditType" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-accounting-account-type" data-id="{{ $row->id }}" data-name="{{ $row->name }}"><i class="fa fa-edit"></i></a>
@endcan

@can('accounting-account-types.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="accounting-account-types/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.cuenta_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan