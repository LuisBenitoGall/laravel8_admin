@can('products.edit')
    {{-- Estado: --}}
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.producto_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="products/status"></span>
    </label>
@endcan

{{-- Ver --}}
<a href="{{ route('products.show', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ trans('textos.producto_ver') }}"><i class="fa fa-eye"></i></a>

@can('products.edit')
    {{-- Translations --}}
    <a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="{{ ucfirst(trans('textos.traduccion')) }}" data-toggle="modal" data-target="#modal-translations" data-id="{{ $row->id }}" data-schema="products"><i class="fa fa-globe"></i></a>

    {{-- Configuraciones de venta - condiciones: 
        1. Si el producto está en venta.
    --}}
    {{-- @if($row->on_sale)
        <a href="{{ route('product-sales-setups.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ trans('textos.') }}"><i class="fa fa-cogs"></i></a>    
    @endif --}}

    {{-- Editar --}}
    <a href="{{ route('products.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('products.destroy')
    <form method="POST" action="{{ route('products.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.producto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan