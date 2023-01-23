@can('staff-pick.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.proceso_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="staff-pick/status"></span>
    </label>

    {{-- Editar --}}
	<a href="{{ route('staff-pick.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('staff-pick.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="{{ route('staff-pick.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.proceso_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan