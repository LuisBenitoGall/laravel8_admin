@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('effects.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_efectos') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('effects.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }} 
					<input type="hidden" name="currency_id" value="{{ $currency->id }}">

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.efecto_nuevo_selec_cliente_proveedor') !!}</p>
						</div>
					</div>

					<div class="form-group row">
						{{-- Clientes --}}
						<label for="customer" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="customer" id="search_customer" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>
			            </div>

			            <input type="hidden" name="customer_id" id="customerId">

			            {{-- Proveedores --}}
			            <label for="provider" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="provider" id="search_provider" placeholder="{!! ucfirst(trans('textos.proveedor')) !!}" maxlength="150"/>
			            </div>

			            <input type="hidden" name="provider_id" id="providerId">
			        </div>					

 					<div class="form-group row">
						{{-- Facturas --}}
						{{-- <label for="invoices" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.facturas')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="invoices[]" class="select2" multiple="true" :options="[]" id="effectInvoices" required/>
						</div> --}}

						{{-- Cuenta bancaria propia --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria_propia')) !!}*</label>
						<div class="col-lg-4">
							@php
							$featured_account_ = $featured_account? $featured_account->id:'';
							@endphp
							<x-select name="my_bank_account" class="select2" :options="$bank_accounts" value="{{ $featured_account_ }}" label="cuenta_selec" required/>
						</div>

						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria_ajena')) !!} <span id="accountOf"></span>*</label>
						<div class="col-lg-4">
							<x-select name="bank_account" class="select2" :options="[]" id="effectBankAccount" label="cuenta_selec" required disabled/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha vencimiento --}}
						<label for="date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}*</label>
						<div class="col-lg-2">
							<x-date-picker name="date" :format="$languages[config('app.locale')][4]" required value="{{ date('d/m/Y') }}"/>
						</div>
					
						{{-- Métodos de pago --}}
						<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}*</label>
						<div class="col-lg-2">
							<x-select name="payment_method" class="select2" :options="$payment_methods" label="metodo_selec" required/>
						</div>

						{{-- Estado del pago --}}
						<label for="payment_status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_estado')) !!}</label>
						<div class="col-lg-2">
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

@push('scripts')
<script>
$(function(){
	//Selector cuentas bancarias y facturas
	$('#search_customer, #search_provider').on('change', function(){
		var id = $(this).val();
		var role_ = $(this).attr('name');

		//Limpiamos el concepto no seleccionado:
		if(role_ == 'customer_id'){
			$('#provider-selector').val('').trigger('change.select2');
			$('#accountOf').text('cliente');
		}else{
			$('#customer-selector').val('').trigger('change.select2');
			$('#accountOf').text('proveedor');
		}

		$('#effectInvoices').val('').trigger('change.select2');

		//Facturas:
		$.get("/admin/invoices/"+id+"/for-effects/"+role_, function(response, state){
		for(i=0; i<response.length; i++){
		$('#effectInvoices').append('<option value="'+response[i].id+'">'+response[i].invoice_+'</option>');
		}
		});

		//Cuentas bancarias:
		$.get("/admin/bank-accounts/"+id+"/for-effects", function(response, state){
			if(response){
				$('#effectBankAccount').removeAttr('disabled');
				for(i=0; i<response.length; i++){
					var account_ = response[i].entity+' - '+response[i].iban+' '+response[i].office+' '+response[i].dc+' '+response[i].account;
					var selected = response[i].featured == 1? 'selected':'';

					$('#effectBankAccount').append('<option value="'+response[i].id+'" '+selected+'>'+account_+'</option>');
				}
			}else{
				$('#effectBankAccount').attr('disabled', 'disabled');
			}
		});
	});
});
</script>
@endpush

@endsection

