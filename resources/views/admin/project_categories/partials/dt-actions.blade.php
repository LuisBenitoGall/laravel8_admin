@can('projects.edit')
	@php
	$selected = in_array($row->id, $categories)? 1:'0';
	@endphp

	{{-- Vincular / desvincular categoría a proyecto --}}
	<span style="display: none">{{ $selected }}</span>
	<label class="custom-chk-rdb btnProjectCategory" rel="tooltip" title="{{ trans('textos.categoria_on_off') }}">
        <input type="checkbox" name="status" @if($selected == 1) checked @endif><span class="chk" data-id="{{ $row->id }}"></span>
    </label>
@endcan