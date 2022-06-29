@can('groups.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.grupo_on_off') }}">
        <input type="checkbox" name="status" @if($group->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $group->id }}" data-ctrl="groups/status"></span>
    </label>

    {{-- Editar --}}
	<a href="{{ route('groups.edit', $group) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('groups.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="groups/{{ $group->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.grupo_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan