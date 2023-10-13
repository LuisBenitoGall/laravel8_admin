{{-- Ver --}}
<a href="{{ route('products.show', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ trans('textos.producto_ver') }}"><i class="fa fa-eye"></i></a>

@can('products.edit')
	{{-- Editar --}}
    <a href="{{ route('products.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}"><i class="fa fa-edit"></i></a>
@endcan