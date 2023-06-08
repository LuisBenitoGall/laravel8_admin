@can('day-types.edit')
	{{-- Estado: --}}
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.tipo_jornada_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="day-types/status"></span>
    </label>

    {{-- Editar --}}
	<a href="{{ route('day-types.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Configuraciones --}}
	<a href="#" class="btn btn-xs btn-info btn-module btnDayTypeConfig" rel="tooltip" title="{{ ucfirst(trans('textos.configuracion')) }}" data-toggle="modal" data-target="#modal-config-day-types" data-id="{{ $row->id }}" data-type="{{ $row->name }}"><i class="fa fa-cog"></i></a>

	{{-- Criterios  --}}
	<a href="#" class="btn btn-xs btn-info btn-module btnSetCriterion" rel="tooltip" title="{{ ucfirst(trans('textos.criterios')) }}" data-toggle="modal" data-target="#modal-set-criterions" data-id="{{ $row->id }}" data-criterion="{{ $row->criterion_id }}"><i class="fa fa-filter"></i></a>
@endcan

@can('day-types.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="{{ route('day-types.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.jornada_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan