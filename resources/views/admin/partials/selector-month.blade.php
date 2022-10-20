<label for="month" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.month')) !!} </label>
<div class="col-lg-2">
	@php
		$months = App\Helpers\Helpers::yearMonths();
	@endphp

	<x-select name="month" class="select2" label="selecciona_month" :options="$months" value="{{ $selected_month }}"/>
</div>
