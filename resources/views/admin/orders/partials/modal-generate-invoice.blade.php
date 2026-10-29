<div class="modal fade modal-generate-invoice" id="modal-generate-invoice" tabindex="-1" role="dialog" aria-labelledby="editLine" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editLine">{!! ucfirst(trans('textos.factura_generar')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	{{-- Se pueden generar facturas si existe la cuenta contable de ventas del proveedor --}}
            	@if($provider_accounting_account_default)
	                <form method="post" autocomplete="off" action="{{ route('invoices.create-from-order') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="order_id" id="newInvoiceOrderId" value="@isset($order) {{ $order->id }} @endisset">

	                    <p class="text-warning">{{ trans('textos.factura_generar_aviso') }}</p>

	                    {{-- Importe pendiente de facturar --}}
	                    @php
	                    $invoiced_ = $invoiced? $invoiced->invoiced_amount:'0';
	                    @endphp

	                    <p>{{ trans('textos.pendiente_facturar') }} <b>{{ round($order_amount - $invoiced_, 2) }}{{ $currency->symbol }}</b></p>

	                    <div class="form-group row my-4">
	                    	{{-- Patrones de factura --}}
	                    	@if(count($invoice_patterns))
	                    		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!}</label>
								<div class="col-md-4">
									<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$invoice_patterns" info="invoice-pattern-num"/>
	                    		</div>
	                    	@endif

	                    	{{-- Fecha fiscal --}}
	                    	<label for="fiscal_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_fiscal')) !!}</label>
							<div class="col-md-4">
								<x-date-picker name="fiscal_date" :format="$languages[config('app.locale')][4]"/>
							</div>
	                    </div>

	                    <div class="form-group row mt-4">
	                    	<div class="col-12">
	                    		{{-- Factura íntegra --}}
		                    	<label class="custom-chk-rdb mr-5" style="margin-top: 2px;">
				    				<input type="radio" name="modality" class="rdbInvoiceModality" value="t" required>
				    				<span class="rdb"></span>
									{{ trans('textos.factura_integra') }}								
								</label>

								{{-- Factura parcial --}}
								<label class="custom-chk-rdb mr-5" style="margin-top: 2px;">
				    				<input type="radio" name="modality" class="rdbInvoiceModality" value="p" required>
				    				<span class="rdb"></span>
									{{ trans('textos.factura_avance') }}
								</label>

								{{-- Previsión de facturación --}}
								@if($next_prevision)
									<label class="custom-chk-rdb" style="margin-top: 2px;">
					    				<input type="radio" name="modality" class="rdbInvoiceModality" value="{{ $next_prevision->id }}" required>
					    				<span class="rdb"></span>
										{{ trans('textos.prevision_facturacion') }} {{ $next_prevision->percent }}%
									</label>
								@endif
							</div>

							@if($errors->has('modality'))
								<div class="col-12">
				                	<span class="invalid-feedback" role="alert">
				                  		<strong>{{ $errors->first('modality') }}</strong>
				                	</span>
				                </div>
				            @endif

				            @if($errors->has('advance_amount'))
				            	<div class="col-12">
				                	<span class="invalid-feedback" role="alert">
				                  		<strong>{{ $errors->first('advance_amount') }}</strong>
				                	</span>
				                </div>
				            @endif

				            @if($errors->has('advance_percent'))
				            	<div class="col-12">
				                	<span class="invalid-feedback" role="alert">
				                  		<strong>{{ $errors->first('advance_percent') }}</strong>
				                	</span>
				                </div>
				            @endif
	                    </div>

	                    <div class="advance-invoice-fields mt-4" style="display: none;">
	                    	<p class="text-warning">{{ trans('textos.factura_avance_aviso') }}</p>

		                    <div class="form-group row">
		                    	{{-- Por cantidad --}}
		                    	<label for="advance_amount" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
								<div class="col-lg-3">
									<x-input type="text" name="advance_amount" id="advance_amount" class="text-right set-decimals" autocomplete="off" maxlength="15" value="" group="right" groupvalue="{{ $currency->symbol }}"/>
								</div>

								{{-- Por porcentaje --}}
								<label for="advance_percent" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.porcentaje')) !!}</label>
								<div class="col-lg-3">
									<x-input type="text" name="advance_percent" id="advance_percent" class="text-right set-decimals percent" autocomplete="off" maxlength="15" value="" group="right" groupvalue="%"/>
								</div>
		                    </div>
	                    </div>

	                    {{-- Cuentas contables de cliente --}}
	                    @if($customer_accounting_accounts->count() == 1)
	                    	<input type="hidden" name="customer_accounting_account" value="{{ $customer_accounting_accounts[0]->id }}">

	                    @elseif($customer_accounting_accounts->count() > 1)
	                    	<div class="form-group row mt-4">
	                    		<label for="customer_accounting_account" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}*</label>	
	                    		<div class="col-lg-9">
	                    			@php
		                            $customer_accounting_accounts_ = [];
		                            @endphp

		                            @foreach($customer_accounting_accounts as $caa)
		                                @php
		                                $customer_accounting_accounts_[$caa->id] = $caa->account.' - '.$caa->name;
		                                @endphp
		                            @endforeach

		                            <x-select name="customer_accounting_account" class="select2" :options="$customer_accounting_accounts_" label="cuenta_contable_selec" required/>		
	                    		</div>
	                    	</div>	
	                    @endif

	                    <x-btn-submit name="submit_generate_invoice" label="{{ trans('textos.guardar') }}"/>
	                </form>
	            
	            @else
	            	<h4 class="text-warning">
	            		{{ trans('textos.cuenta_contable_ventas_defecto') }}
	            	</h4>

	            	@can('accounting-accounts.create')
		            	<a href="{{ route('accounting-accounts.index') }}" class="btn bt-xs btn-info mt-3">
		            		<i class="fa fa-file mr-1"></i>
		            		{{ trans('textos.cuentas_contables') }}
		            	</a>
		            @endcan
	            @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	//Selector de opción de facturación:
	$('.rdbInvoiceModality').on('change', function(){
        var value = $(this).val();
        if(value == 'p'){
            $('.advance-invoice-fields').slideDown();

        }else{
            $('#advance_amount, #advance_percent').val('');
            $('.advance-invoice-fields').slideUp();
        }
    });

	//Toogle entre importe íntegro o por porcentaje:
	$('#advance_amount').on('change', function(){
		var valor = $(this).val();
		if(valor != ''){
			$('#advance_percent').val('').prop("disabled", true);
		}else{
			$('#advance_percent').prop("disabled", false);
		}
	});
});
</script>

@endpush