@can('schedules.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.evento_tipo_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="schedule-event-types/status"></span>
    </label>

    {{-- Traducciones --}}
    <a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="{{ ucfirst(trans('textos.traducciones')) }}" data-toggle="modal" data-target="#modal-translations" data-id="{{ $row->id }}" data-schema="schedule_event_types"><i class="fa fa-globe"></i></a>

    {{-- Editar --}}
	<a href="{{ route('schedule-event-types.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar --}}
	<form method="POST" action="schedule-event-types/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.evento_tipo_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan