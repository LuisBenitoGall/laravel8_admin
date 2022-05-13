<label for="year" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.any')) !!} </label>
<div class="col-lg-2">
	@php
	$max_year = date('Y')+1;
	$years = [];
	for($i=$max_year; $i>=config('constants.MIN_YEAR_'); $i--){
		$years[$i] = $i;
	}
	@endphp

	<x-select name="year" class="select2" label="selecciona_any" :options="$years" value="{{ $selected_year }}"/>
</div>
