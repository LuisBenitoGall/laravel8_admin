@can('seats.edit')
	{{-- Editar asiento contable --}}
	<a target="_blank" href="{{ route('seats.edit', $row->seat_id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.asiento_contable_editar')) }}"><i class="fa fa-edit"></i></a>
@endcan