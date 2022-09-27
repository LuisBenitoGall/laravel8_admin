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