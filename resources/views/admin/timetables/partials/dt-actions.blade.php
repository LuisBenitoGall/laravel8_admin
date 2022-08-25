@can('users.edit')
	{{-- Editar --}}
	<a target="_blank" href="{{ route('users.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.usuario_editar')) }}">
        <i class="fa fa-edit"></i>
    </a>
@endcan

@can('employees-timetable.edit')
	{{-- Ver horarios --}}
	<a target="_blank" href="{{ route('users.timetables', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.horarios_ver')) }}">
        <i class="fa fa-clock"></i>
    </a>
@endcan
