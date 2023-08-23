{{-- Selector de producto --}}
@if($selector)
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.correspondencia_ref') }}">
        <input type="checkbox" class="chkSetRef" name="on" @if($row->provider_product_id) checked @endif value="{{ $row->id }}"><span class="chk"></span>
    </label>
@endif

{{-- Ver --}}
@php
$product_id = $row->provider_product_id? $row->provider_product_id:$row->id;
@endphp

<a target="_blank" href="{{ route('products.show', $product_id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

{{-- Comprar --}}
<a href="{{ route('orders.order-from-product', $row->id) }}" class="btn btn-xs btn-info btn-confirm" data-msg="{{ trans('textos.producto_compra_confirm') }}" rel="tooltip" title="{{ ucfirst(trans('textos.producto_comprar')) }}"><i class="fa fa-shopping-cart"></i></a>

{{-- Eliminar --}}
@can('products.edit')
    @if(!$selector)
        <form method="POST" action="{{ route('product-providers.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
            <input name="_method" type="hidden" value="DELETE">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.proveedor_confirm_desvincular')) }}"><i class="fa fa-trash"></i></button>
        </form>
    @endif
@endcan