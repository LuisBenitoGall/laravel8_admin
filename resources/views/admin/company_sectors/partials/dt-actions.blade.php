@can('companies.edit')
	@php
	$selected = in_array($row->id, $categories)? 1:'0';
	@endphp

	{{-- Vincular / desvincular sector a empresa --}}
	<span style="display: none">{{ $selected }}</span>
	<label class="custom-chk-rdb btnCompanySector" rel="tooltip" title="{{ trans('textos.sector_on_off') }}">
        <input type="checkbox" name="status" @if($selected == 1) checked @endif><span class="chk" data-id="{{ $row->id }}"></span>
    </label>
@endcan