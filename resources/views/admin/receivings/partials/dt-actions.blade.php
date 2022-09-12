@can('deliveries.edit')
	{{-- Ver detalles --}}
	<a href="#" class="btn btn-xs btn-info btnSeeItem" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

	{{-- Editar --}}
	<a target="_blank" href="{{ route('receiving.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.recepcion_tramitar')) }}"><i class="fa fa-edit"></i></a>
@endcan