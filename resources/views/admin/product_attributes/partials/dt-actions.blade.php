@can('products.edit')
	{{-- Editar --}}
	<a href="#" data-toggle="modal" data-target="#modal-edit-attribute" class="btn btn-xs btn-info btnEditAttribute" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-id="{{ $row->paId }}" data-value="{!! $row->value !!}" data-type="{{ $row->datatype }}" data-values="{{ $row->values }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar --}}
	<form method="POST" action="/admin/product-attributes/{{ $row->paId }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.atributo_producto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan