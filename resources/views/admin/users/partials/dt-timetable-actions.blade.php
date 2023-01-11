{{-- Autorizar conceptos --}}
@can('employees-timetable.edit')
    @if(!$row['authorized'])
        <label class="switch mr-2 btnAuthorizeConcept" data-id="{{ $row['id'] }}" rel="tooltip" title="Autorizar concepto">
            <input type="checkbox" class="btnSlideAllPermissions">
            <span class="slider round"></span>
        </label>
    @endif
@endcan

{{-- Revisión --}}
<label class="custom-chk-rdb" rel="tooltip" title="{{ ucfirst(trans('textos.solicitar_revision')) }}">
    <input type="checkbox" name="revision" @if($row['revision'] == 1) checked @endif><span class="chk chkRev" data-id="{{ $row['id'] }}"></span>
</label>

@can('employees-timetable.edit')
	{{-- Editar --}}
    @if($row['edit_timetable'])
    	<a href="#" class="btn btn-xs btn-info btnRegistroHorario" rel="tooltip" title="{{ ucfirst(trans('textos.registro_horario_editar')) }}" data-toggle="modal" data-target="#modal-edit-timetable" data-id="{{ $row['id'] }}" data-concepto="{{ $row['name'] }}" data-icono="{{ $row['icon'] }}" data-flag="{{ $row['flag'] }}">
            <i class="fa fa-edit"></i>
        </a>
    @endif

    {{-- Añadir registro horario: --}}
    @if($row['add_timetable'])
        <a href="#" class="btn btn-xs btn-info btnAddRegistroHorario" rel="tooltip" title="{{ ucfirst(trans('textos.registro_horario_add')) }}" data-toggle="modal" data-target="#modal-add-timetable" data-date="{{ $row['date_order'] }}" data-fecha="{{ $row['fecha'] }}">
            <i class="fa fa-plus"></i>
        </a>
    @endif
@endcan

@can('employees-timetable.destroy')
	{{-- Eliminar --}}
    @if($row['edit_timetable'])
    	<form method="POST" action="{{ route('timetable.destroy', $row['id']) }}" class="frm-inline" role="form" accept-charset="UTF-8">
            <input name="_method" type="hidden" value="DELETE">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.registro_horario_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.registro_horario_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
        </form>
    @endif
@endcan
