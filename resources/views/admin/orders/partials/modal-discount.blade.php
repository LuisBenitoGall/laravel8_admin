@if($order->provider_id == session('currentCompany') && !$order->discount)
	<div class="modal fade modal-budget-discount" id="modal-budget-discount" tabindex="-1" role="dialog" aria-labelledby="editLine" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="editLine">{!! ucfirst(trans('textos.descuento_presupuesto')) !!} <b></b></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	                <form method="post" autocomplete="off" action="{{ route('orders.add-discount', $order->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                	@method('PUT')
	                    {{ csrf_field() }}
	                    <input type="hidden" name="return" value="{{ $return }}">

	                    <div class="row">
	                    	<p class="col-12 text-warning mb-4">
	                    		<span class="btn btn-circle btn-circle-mini btn-warning">
	                    			<i class="fa fa-exclamation"></i>
	                    		</span>
	                    		{!! trans('textos.descuento_presupuesto_texto') !!}
	                    	</p>
	                    </div>

	                    <div class="form-group row">
							{{-- Descuento % --}}
							<label for="discount_percent" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.porcentaje')) !!}</label>
							<div class="col-lg-4">
								<x-input type="text" name="discount_percent" class="text-right set-decimals set-discount discount-percent" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-discount-percent"/>
							</div>
						</div>

	                    <div class="form-group row">
	                    	{{-- Descuento Importe --}}
	                    	<label for="discount_amount" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
							<div class="col-lg-4">
								<x-input type="text" name="discount_amount" class="text-right set-decimals set-discount discount-amount" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}" info="budget-discount-amount"/>
							</div>
						</div>

						<x-btn-submit name="submit_order_discount" label="{{ trans('textos.guardar') }}"/>
	                </form>    
	            </div>
	        </div>
	    </div>
	</div>
@endif