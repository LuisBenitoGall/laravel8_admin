{{-- Ver --}}
<a target="_blank" href="{{ route('products.show', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ trans('textos.producto_ver') }}"><i class="fa fa-eye"></i></a>

{{-- Ver inventario --}}
<a href="#" data-product="{{ $row->id }}" class="btn btn-xs btn-info btn-module btnSeeItem" rel="tooltip" title="{{ trans('textos.inventario_ver') }}"><i class="fa fa-database"></i></a>

@can('products.edit')
	{{-- Editar --}}
    <a target="_blank" href="{{ route('products.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}"><i class="fa fa-edit"></i></a>
@endcan