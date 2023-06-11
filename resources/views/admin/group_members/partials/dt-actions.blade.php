@can('groups.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.responsable_on_off') }}">
        <input type="checkbox" name="status" @if($row->responsible == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="/admin/group-members/status"></span>
    </label>

    {{-- Editar --}}
	<a href="#" class="btn btn-xs btn-info btnEditMember" data-toggle="modal" data-target="#modal-edit-member" data-id="{{ $row->id }}" data-member="{{ $row->name }} {{ $row->surname }}" data-rol="{{ $row->rol }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar --}}
	<form method="POST" action="{{ route('group-members.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.miembro_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan