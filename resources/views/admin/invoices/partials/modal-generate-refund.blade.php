<div class="modal fade modal-generate-refund" id="modal-generate-refund" tabindex="-1" role="dialog" aria-labelledby="generateRefund" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="generateRefund">{!! ucfirst(trans('textos.abono_generar')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('invoices.refund') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                    <p class="text-warning">{{ trans('textos.abono_generar_aviso') }}</p>

                    <div class="form-group row mt-4">
                    	{{-- Tipo de abono --}}
                    	<div class="col-12">
	                    	<label class="custom-chk-rdb mr-5" style="margin-top: 2px;">
			    				<input type="radio" name="refund" class="rdbRefund" value="t" required>
			    				<span class="rdb"></span>
								{{ trans('textos.abono_integro') }}								
							</label>

							<label class="custom-chk-rdb" style="margin-top: 2px;">
			    				<input type="radio" name="refund" class="rdbRefund" value="p" required>
			    				<span class="rdb"></span>
								{{ trans('textos.abono_parcial') }}
							</label>
						</div>

						@if($errors->has('refund'))
							<div class="col-12">
			                	<span class="invalid-feedback" role="alert">
			                  		<strong>{{ $errors->first('refund') }}</strong>
			                	</span>
			                </div>
			            @endif

			            @if($errors->has('concept'))
			            	<div class="col-12">
			                	<span class="invalid-feedback" role="alert">
			                  		<strong>{{ $errors->first('concept') }}</strong>
			                	</span>
			                </div>
			            @endif

			            @if($errors->has('refund_amount'))
			            	<div class="col-12">
			                	<span class="invalid-feedback" role="alert">
			                  		<strong>{{ $errors->first('refund_amount') }}</strong>
			                	</span>
			                </div>
			            @endif
                    </div>

                    <div class="refund-fields mt-4">
                    	<p class="text-warning">{{ trans('textos.abono_concepto_aviso') }}</p>

                    	{{-- Concepto --}}
	                    <div class="form-group row">
	                    	<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
							<div class="col-lg-10">
								<x-input type="text" name="concept" id="refund_concept"  autocomplete="off" maxlength="155" info="invoice-old-num"/>
							</div>
						</div>

						{{-- Importe --}}
						<div class="form-group row">
	                    	<label for="refund_amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!}*</label>
							<div class="col-lg-4">
								<x-input type="text" name="refund_amount" id="refund_amount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}"/>
							</div>
	                    </div>
                    </div>

                    <x-btn-submit name="submit_generate_refund" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>