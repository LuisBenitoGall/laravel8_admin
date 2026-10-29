<form method="post" autocomplete="off" action="{{ route('orders.update-conditions', $order) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $order->id }}">

	<div class="form-group row">
		{{-- Cuenta contable --}}
		<label for="accounting_sale_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}</label>
		<div class="col-lg-3">
			@php
			$accountingSaleId = $order->accounting_sale_id? $order->accounting_sale_id:($provider_accounting_account_default? $provider_accounting_account_default->id:false);
			@endphp

			@if($accountingSaleId)
				<x-select name="accounting_sale_id" class="select2" label="cuenta_selecciona" :options="$provider_accounting_accounts" info="" value="{{ $accountingSaleId }}"/>
			@endif
		</div>

		{{-- Nº de plazos --}}
		<label for="num_deadlines" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plazos_num')) !!}</label>
		<div class="col-lg-1">
			<x-input type="text" name="num_deadlines" class="text-right" autocomplete="off" maxlength="3" info="pay-conditions-deadlines" value="{{ $order->num_deadlines }}"/>
		</div>

		{{-- Días entre plazos --}}
		<label for="days_between_deadlines" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plazos_intervalo')) !!}</label>
		<div class="col-lg-1">
			<x-input type="text" name="days_between_deadlines" class="text-right" autocomplete="off" maxlength="3" info="pay-conditions-deadlines-days" value="{{ $order->days_between_deadlines }}"/>
		</div>		
	</div>

	<x-btn-submit name="submit_conditions" label="{{ trans('textos.guardar') }}"/>
</form>	