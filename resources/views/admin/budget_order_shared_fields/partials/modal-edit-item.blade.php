<div class="modal fade modal-edit-line" id="modal-edit-line" tabindex="-1" role="dialog" aria-labelledby="editLine" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editLine">{!! ucfirst(trans('textos.linea_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('items.update') }}" id="frmEditItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="itemId_">
                    @if($order)
                    	<input type="hidden" name="order_id" value="{{ $order->id }}">
                    @endif
                    @if($budget)
                    	<input type="hidden" name="budget_id" value="{{ $budget->id }}">
                    @endif
                    <input type="hidden" name="environment" value="{{ $environment_ }}">

                    <div class="form-group row">
						{{-- Producto --}}
                    	<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!} *</label>
						<div class="col-lg-10">
							<x-input type="text" name="concept" autocomplete="off" id="itemConcept_" maxlength="150" required/>	
						</div>
					</div>

					<div class="form-group row">
						{{-- Cantidad --}}
                    	<label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!} *</label>
						<div class="col-lg-2">
							<x-input type="text" name="qty" class="text-right set-decimals" id="itemQty_" autocomplete="off" maxlength="10" value="1" required/>
						</div>

						{{-- Jornadas --}}
						<label for="days" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.jornadas')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="days" class="text-right set-decimals" id="itemDays_" autocomplete="off" maxlength="10"/>
						</div>

						{{-- Precio --}}
						<label for="price" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.precio_unidad')) !!} </label>
						<div class="col-lg-2">
							@php
							$readonly = session('currentCompany') == $shared->customer_id? true:false;
							@endphp

							<x-input type="text" name="price" class="text-right set-decimals" autocomplete="off" maxlength="10" id="itemPrice_" value="0.00" group="right" groupvalue="{{ $currency->symbol }}" readonly="{{ $readonly }}"/>
						</div>
					</div>

					@if(session('currentCompany') == $shared->provider_id)
						<div class="form-group row">
							{{-- Descuento --}}
							@if(!$shared->discount)
								<label for="discount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descuento')) !!} </label>
								<div class="col-lg-2">
									<x-input type="text" name="discount" id="itemDiscount_" class="text-right set-decimals" autocomplete="off" maxlength="5" value="0.00" group="right" groupvalue="%"/>
								</div>

								@if($customer_settings && $customer_settings->discount)
									<div class="col-lg-2">
										<button class="btn btn-warning btnApplyDiscount" data-discount="{{ $customer_settings->discount }}">
											<i class="fa fa-percent"></i>
											{!! trans('textos.descuento_clientes_aplicar') !!} ({{ $customer_settings->discount }}%)
										</button>
									</div>
								@endif
							@else
								<div class="col-lg-8">
									<p class="text-warning">{!! trans('textos.descuento_pedido_aplicado_aviso') !!}</p>
								</div>
							@endif

							{{-- IVA. Sólo en pedidos. En presupuestos no se tiene en cuenta este impuesto --}}
							@if($environment_ == 'orders')
								<label for="iva" class="col-lg-2 col-form-label">IVA</label>
								<div class="col-lg-2">
									<x-input type="text" name="iva" class="text-right set-decimals" id="itemIva_" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
								</div>
							@endif
						</div>
					@endif

					<div class="form-group row">
						{{-- Observaciones --}}
                    	<label for="observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-12">
							<x-input-textarea name="observations" class="editor" id="itemObservations_"/>
						</div>
					</div>

                    <x-btn-submit name="submit_update_items" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>