<label for="month" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.mes')) !!} </label>
<div class="col-lg-2">
	@php
		$months = App\Helpers\Helpers::yearMonths();
	@endphp

	<x-select name="month" class="select2" label="mes_selec" :options="$months" value="{{ $selected_month }}"/>
</div>
