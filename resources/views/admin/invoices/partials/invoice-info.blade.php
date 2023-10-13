<form method="post" autocomplete="off" action="{{ route('invoices.update', $invoice) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de factura --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
			{{-- Patrón de numeración - condiciones:
			 	1. Si existen patrones.
				3. Exclusivo para proveedor.
				...
			--}}

			@if($patterns && $order->provider_id == session('currentCompany'))
				@if($patterns->count())
					@php
					$patterns_ = [];
					foreach($patterns as $ptn){
						$patterns_[$ptn->id] = $ptn->name;
					}
					@endphp

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="invoice-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($invoice->invoice_num)
						{{ $invoice->invoice_num }}
					@elseif($invoice->invoice_old_num)
						{{ $invoice->invoice_old_num }}	
					@else
						{{ $invoice->id }}
					@endif
				</u></b></label>
			@endif
		</div>

		{{-- Factura nº manual --}}
		<label for="invoice_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_antigua')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="invoice_old_num" value="{{ $invoice->invoice_old_num }}" autocomplete="off" maxlength="150" info="invoice-old-num" readonly="{{ $readonly }}"/>
		</div>

		{{-- Documento --}}
		@if(!$is_sale)
			@if($invoice->doc)
				<label for="doc" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_ver')) !!}</label>
				<div class="col-md-2 col-lg-1 pt-2">
					<a target="_blank" href="{{ asset('docs/invoices/'.$invoice->doc) }}" rel="tooltip" title="{{ trans('textos.documento_ver') }}">
						<i class="fa fa-file" style="font-size: 18px;"></i>
					</a>

					{{-- Eliminar documento --}}
					<a href="{{ route('invoices.delete-doc', $invoice) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.documento_eliminar') }}" data-msg="{{ ucfirst(trans('textos.documento_eliminar_confirm')) }}">
						<i class="fa fa-trash text-danger" style="font-size: 18px;"></i>
					</a>
				</div>
			@else
				<label for="doc" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_adjuntar')) !!}</label>
				<div class="col-md-2">
					<x-input-file name="doc"/>

					@if($errors->has('doc'))
	                    <span class="invalid-feedback" role="alert">
	                      <strong>{{ $errors->first('doc') }}</strong>
	                    </span>
	                @endif
				</div>	
			@endif
		@endif
	</div>

	<div class="form-group row">
		{{-- Fecha registro --}}
		<label for="fiscal_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_registro')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$fiscal_date = $invoice->fiscal_date != ''? \Carbon\Carbon::parse($invoice->fiscal_date)->format($languages[session()->get('locale')][4]):'';
			@endphp	

			<x-date-picker name="fiscal_date" value="{{ $fiscal_date }}" :format="$languages[config('app.locale')][4]" disabled="{{ $disabled }}"/>

			<input type="hidden" id="fiscalDate" value="{{ $invoice->fiscal_date }}">
		</div>

		{{-- Fecha vencimiento --}}
		<label for="expiration_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			//Calculamos la fecha de vencimiento por defecto a partir de los datos de días para vencimiento y día de pago existentes en la configuración entre cliente y proveedor:
			$default_expiration_date = $invoice->fiscal_date && $provider_settings->pay_after_days && $provider_settings->payday? App\helpers\helpers::calculateExpirationDate($invoice->fiscal_date, $provider_settings->pay_after_days, $provider_settings->payday):false;

			$expiration_date = $invoice->expiration_date != ''? \Carbon\Carbon::parse($invoice->expiration_date)->format($languages[session()->get('locale')][4]):($default_expiration_date? \Carbon\Carbon::parse($default_expiration_date)->format($languages[session()->get('locale')][4]):'');
			@endphp	

			<x-date-picker name="expiration_date" id="expirationDate" value="{{ $expiration_date }}" :format="$languages[config('app.locale')][4]" disabled="{{ $disabled }}"/>
		</div>

		{{-- Fecha de comprobante --}}
		@if(!$is_sale)
			<label for="check_in_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_comprobante')) !!}</label>
			<div class="col-md-4 col-lg-2">
				@php
				$check_in_date = $invoice->check_in_date != ''? \Carbon\Carbon::parse($invoice->check_in_date)->format($languages[session()->get('locale')][4]):'';
				@endphp	

				<x-date-picker name="check_in_date" value="{{ $check_in_date }}" :format="$languages[config('app.locale')][4]"/>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Estado - condiciones: 
			1. Ventas.
			2. Proveedores sin cuenta.
		--}}
		@if($is_sale || !$provider_account)
			<label for="status" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
			<div class="col-md-4 col-lg-2">
				@php
				$estados = App\Helpers\helpers::invoiceStatus();
				@endphp

				<x-select name="status" class="select2" label="indica_estado" :options="$estados" value="{{ $invoice->status }}" disabled="{{ $disabled }}"/>	
			</div>
		@endif

		{{-- Ajuste IVA --}}
		<label for="iva_adjust" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.iva_ajuste')) !!}</label>
		<div class="col-lg-1">
			<x-input type="text" name="iva_adjust" class="text-right set-decimals" autocomplete="off" maxlength="6" group="right" groupvalue="{{ $currency->symbol }}" value="{{ $invoice->iva_adjust }}"/>
		</div>
	</div>
	
	@if(!$is_sale)
		{{-- Conceptos contables. Sólo para compras --}}
		<div class="form-group row">
			{{-- IRPF --}}
			<label for="irpf" class="col-lg-2 col-form-label">IRPF</label>
			<div class="col-lg-1">
				<x-input type="text" name="irpf" class="text-right set-decimals" autocomplete="off" maxlength="6" group="right" groupvalue="%" value="{{ $invoice->irpf }}"/>
			</div>

			{{-- Estructura --}}
			@php
			$as_id = $invoice->accounting_structure_id? $invoice->accounting_structure_id:($provider_settings? $provider_settings->accounting_concept_id:'');
			@endphp

			<label for="accounting_structure" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.estructura_gastos')) !!}</label>
			<div class="col-md-3">
				<x-select name="accounting_structure" class="select2" id="accountingStructure" label="concepto_selec" :options="$concepts" value="{{ $as_id }}" info="invoice-accounting-structure"/>
			</div>

			{{-- Cuenta de gastos --}}
			@php
			$ac_id = $invoice->accounting_concept_id? $invoice->accounting_concept_id:'';
			$disabled = count($structure_concepts)? false:true;
			@endphp
			
			<label for="accounting_concept" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.cuenta_gastos')) !!}</label>
			<div class="col-md-2">
				<x-select name="accounting_concept" class="select2" id="accountingConcept" label="" :options="$structure_concepts" value="{{ $ac_id }}" disabled="{{ $disabled }}"/>
			</div>
		</div>

		{{-- Condiciones de pago --}}
		<div class="form-group row">
			{{-- Días para vencimiento --}}
			<label for="pay_after_days" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.dias_vencimiento')) !!}</label>
			<div class="col-lg-1">
				<x-input type="text" name="pay_after_days" class="text-right" id="payAfterDays" autocomplete="off" maxlength="3" info="pay-conditions-days" value="{{ $provider_settings->pay_after_days }}" readonly="{{ $readonly }}"/>
			</div>

			{{-- Fecha de pago --}}
			<label for="payday" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.dia_mes')) !!}</label>
			<div class="col-lg-1">
				<x-input type="text" name="payday" class="text-right max-value" id="payDay" datas="maxvalue:31" autocomplete="off" maxlength="2" info="pay-conditions-pay-day" value="{{ $provider_settings->payday }}" readonly="{{ $readonly }}"/>
			</div>	

			{{-- Métodos de pago --}}
			<label for="payment_method" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.metodos_pago')) !!}</label>
			<div class="col-md-2">
				@php
				$pm = $invoice->payment_method_id? $invoice->payment_method_id:($provider_settings->payment_method_id? $provider_settings->payment_method_id:'');
				@endphp

				<x-select name="payment_method" class="select2" id="paymentMethods" label="metodo_selec" :options="$payment_methods" value="{{ $pm }}" disabled="{{ $disabled }}"/>
			</div>
		</div>
	@endif

	{{-- @if(!$is_sale) --}}
		<div class="form-group row">
			{{-- Cuenta bancaria del proveedor --}}
			@php
            $providerBankAccount = $invoice->provider_bank_account_id? $invoice->provider_bank_account_id:$provider_bank_featured;
            @endphp
			<label for="provider_bank_account_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria_proveedor')) !!}</label>
            <div class="col-lg-4">
                <x-select name="provider_bank_account_id" class="select2" label="banco_selec" :options="$provider_bank_accounts" value="{{ $providerBankAccount }}"/>
            </div>

            {{-- Cuenta bancaria del cliente --}}
			@php
            $customerBankAccount = $invoice->bank_account_id? $invoice->bank_account_id:$customer_bank_featured;
            @endphp
			<label for="bank_account_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria_cliente')) !!}</label>
            <div class="col-lg-4">
                <x-select name="bank_account_id" class="select2" label="banco_selec" :options="$customer_bank_accounts" value="{{ $customerBankAccount }}"/>
            </div>
		</div>
	{{-- @endif --}}

	<div class="form-group row">
		{{-- Observaciones --}}
		<label for="observations" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-md-10">
			@php
			//Se editan las observaciones del cliente o del proveedor según sea vista de compra o venta.
			$observations = $is_sale? $invoice->provider_observations:$invoice->customer_observations;
			@endphp

			<x-input-textarea name="observations" class="editor" value="{{ $observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>