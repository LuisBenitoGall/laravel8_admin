@can('orders.edit')
    {{-- Editar --}}
	<a href="{{ route('orders.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan