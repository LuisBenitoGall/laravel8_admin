@can('modules.edit')
	{{-- Estado, sólo para módulos opcionales --}}
    @if($row->level == 3)
		<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.grupo_on_off') }}">
	        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="modules/status"></span>
	    </label>
	@endif

	{{-- Translations --}}
    <a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="{{ ucfirst(trans('textos.traduccion')) }}" data-toggle="modal" data-target="#modal-translations" data-id="{{ $row->id }}" data-schema="modules"><i class="fa fa-globe"></i></a>

    {{-- Editar --}}
	<a href="{{ route('modules.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('modules.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="modules/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.modulo_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan