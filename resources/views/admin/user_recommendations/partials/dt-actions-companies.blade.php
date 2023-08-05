{{-- Estado --}}
<span class="d-none">@if($row->recommendations) 1 @else 0 @endif</span><label class="custom-chk-rdb" rel="tooltip" title="{{ ucfirst(trans('textos.empresa_on_off')) }}">
    <input type="checkbox" name="status" @if($row->recommendations) checked @endif><span class="chk chk-action" data-id="{{ $row->cs_id }}" data-ctrl="/admin/user-recommendations/set-company"></span>
</label>