@can('roles.edit')
	{{-- Editar --}}
	<a href="{{ route('roles.edit', $row) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('roles.destroy')
    {{-- Eliminar: - condiciones:
		1. No puede eliminarse el rol de Super Admin:
    --}}
    @if($row->name != config('constants.SUPER_ADMIN_'))
	    <form method="POST" action="{{ route('roles.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	        <input name="_method" type="hidden" value="DELETE">
	        {{ csrf_field() }}
	        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.eliminar")) !!}" data-msg="{{ ucfirst(trans("textos.rol_confirm_eliminar")) }}"><i class="fa fa-trash"></i></button>
	    </form>
	@endif
@endcan