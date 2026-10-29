<div class="modal fade modal-set-group" id="modal-set-group" tabindex="-1" role="dialog" aria-labelledby="addRemittance" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addRemittance">{!! ucfirst(trans('textos.agrupar_efectos')) !!}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('effects.store') }}"accept-charset="utf-8" enctype="multipart/form-data" role="form" id="frmEffectsGroups">
					{{ csrf_field() }}
					<input type="hidden" name="currency_id" value="{{ $currency->id }}">
					<input type="hidden" name="origin" value="group">
					@if($customer_id)
						<input type="hidden" name="customer_id" value="{{ $customer_id }}">	
					@endif
					@if($provider_id)
						<input type="hidden" name="provider_id" value="{{ $provider_id }}">	
					@endif

					<div class="form-group row">
						{{-- Cuenta bancaria propia --}}
						<label for="bank_account" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.bank_account_propia')) !!}*</label>
						<div class="col-lg-9">
							<x-select name="my_bank_account" class="select2" :options="$my_bank_accounts" value="{{ $featured_account->id }}" label="cuenta_selec" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.bank_account')) !!} <span id="accountOf"></span>*</label>
						<div class="col-lg-9">
							<x-select name="bank_account" class="select2" :options="$bank_accounts" id="effectBankAccount" label="cuenta_selec" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha vencimiento --}}
						<label for="date" class="col-2 col-form-label">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}*</label>
						<div class="col-4">
							<x-date-picker name="date" :format="$languages[config('app.locale')][4]" required value="{{ date('d/m/Y') }}"/>	
						</div>

						{{-- Serie --}}
						{{-- <label for="serie" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.serie')) !!}</label>
						<div class="col-lg-4">
							@php
							$serie_id = Auth::user()->invoice_serie_id?? '';
							@endphp
							<x-select name="serie" class="select2" :options="$series" label="serie_selec" value="{{ $serie_id }}"/>
						</div> --}}
					</div>

					<div class="form-group row">
						{{-- Métodos de pago --}}
						<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="payment_method" class="select2" :options="$payment_methods" label="metodo_selec" required/>
						</div>

						{{-- Estado del pago --}}
						<label for="payment_status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_estado')) !!}</label>
						<div class="col-lg-4">
							<x-select name="payment_status" class="select2" :options="$payment_status" id="effectPaymentStatus" label="estado_selec"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form> 
			</div>
		</div>
	</div>
</div>