@if($conditions)
	<form method="post" autocomplete="off" action="{{ route('customers.update-conditions', $conditions->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
		@method('PUT')
		{{ csrf_field() }}
		<input type="hidden" name="company_id" value="{{ $company->id }}">
		<input type="hidden" name="environment" value="{{ $environment }}">
		<input type="hidden" name="return" value="{{ $return }}">

		<div class="form-group row">
			{{-- Días para vencimiento - condiciones: 
				1. Lo setean los clientes respectos sus proveedores o proveedores para clientes sin cuenta.
			--}}
			<label for="pay_after_days" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.dias_vencimiento')) !!}</label>
			<div class="col-lg-1">
				@php
				$disabled1 = $environment == 'providers' && $company_account? 'disabled':'';
				@endphp
				<x-input type="text" name="pay_after_days" class="text-right" autocomplete="off" maxlength="3" info="pay-conditions-days" value="{{ $conditions->pay_after_days }}" disabled="{{ $disabled1 }}"/>
			</div>

			{{-- Fecha de pago --}}
			<label for="payday" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.dia_mes')) !!}</label>
			<div class="col-lg-1">
				<x-input type="text" name="payday" class="text-right max-value" datas="maxvalue:31" autocomplete="off" maxlength="3" info="pay-conditions-pay-day" value="{{ $conditions->payday }}" disabled="{{ $disabled1 }}"/>
			</div>

			{{-- Métodos de pago - condiciones:
				1. Lo setean los clientes respectos sus proveedores.
			 --}}
			<label for="payment_method" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.metodos_pago')) !!}</label>
			<div class="col-md-2">
				@php
				$disabled2 = $environment == 'customers'? 'disabled':'';
				@endphp
				<x-select name="payment_method" class="select2" id="paymentMethods" label="metodo_selec" :options="$payment_methods" info="pay-conditions-method" value="{{ $conditions->payment_method_id }}" disabled="{{ $disabled2 }}"/>
			</div>
		</div>

		<div class="form-group row">
			{{-- Nº de plazos --}}
			<label for="num_deadlines" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plazos_num')) !!}</label>
			<div class="col-lg-1">
				<x-input type="text" name="num_deadlines" class="text-right" autocomplete="off" maxlength="3" info="pay-conditions-deadlines" value="{{ $conditions->num_deadlines }}" disabled="{{ $disabled1 }}"/>
			</div>

			{{-- Días entre plazos --}}
			<label for="days_between_deadlines" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plazos_intervalo')) !!}</label>
			<div class="col-lg-1">
				<x-input type="text" name="days_between_deadlines" class="text-right" autocomplete="off" maxlength="3" info="pay-conditions-deadlines-days" value="{{ $conditions->days_between_deadlines }}" disabled="{{ $disabled1 }}"/>
			</div>
		</div>

		<div class="form-group row">
			{{-- Método envío de las facturas - condiciones: 
				1. Lo setean los proveedores para sus clientes o si éstos carecen de cuenta.
			--}}
			<label for="billing_comunication" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.envio_facturacion')) !!}</label>
			<div class="col-lg-10">
				@php
				$disabled3 = $environment == 'providers' && $company_account? 'disabled':'';
				@endphp
				<x-input-textarea name="billing_comunication" value="{{ $conditions->billing_comunication }}" disabled="{{ $disabled3 }}"/>
			</div>
		</div>

		<x-btn-submit name="submit_update_conditions" label="{{ trans('textos.guardar') }}"/>
	</form>
@endif