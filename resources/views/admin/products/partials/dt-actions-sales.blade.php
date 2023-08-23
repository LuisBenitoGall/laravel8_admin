{{-- Ver --}}
<a href="{{ route('orders.show', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

@can('orders.edit')
	{{-- Editar --}}
	<a href="{{ route('orders.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan