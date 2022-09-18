{{-- Selector de producto --}}
<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.correspondencia_ref') }}">
    <input type="checkbox" class="chkSetRef" name="on" @if($row->provider_product_id) checked @endif value="{{ $row->id }}"><span class="chk"></span>
</label>

{{-- Ver --}}
<a target="_blank" href="{{ route('products.show', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>