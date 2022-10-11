{{-- Estado --}}
@if( (!$row->calculated_by_percent || $row->calculated_by_percent == 0) && $environment == 'customers')
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.producto_precio_adhoc_on_off') }}">
        <input type="checkbox" name="active" @if($row->priceForCustomer) checked @endif><span class="chk btnCustomerProductStatus" data-product="{{ $row->id }}" data-price="{{ $row->price }}"></span>
    </label>
@endif

{{-- Ver --}}
<a target="_blank" href="{{ route('products.show', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

{{-- Correspondencia de referencia. Determina la referencia de cliente que se corresponde a la del proveedor. --}}
@if($environment == 'providers')
    <a target="_blank" href="#" class="btn btn-xs btn-info btnProviderProduct" rel="tooltip" title="{{ ucfirst(trans('textos.correspondencia_ref')) }}" data-toggle="modal" data-target="#modal-set-reference" data-id="{{ $row->id }}"><i class="fa fa-asterisk"></i></a>
@endif
