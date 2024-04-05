@can('accounting-accounts.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.cuenta_on_off') }}">
		<input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="accounting-accounts/status"></span>
	</label>

	{{-- Editar --}}
	<a href="{{ route('accounting-accounts.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('accounting-accounts.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="accounting-accounts/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
	<input name="_method" type="hidden" value="DELETE">
	{{ csrf_field() }}
	<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.cuenta_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan