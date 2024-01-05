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
                    @if($order->is_order || $environment_ == 'invoices')
                    	<input type="hidden" name="order_id" value="{{ $order->id }}">
                    @endif
                    @if(isset($invoice))
                    	<input type="hidden" name="irpf_from_invoice" value="{{ $invoice->irpf }}">
                    @endif
                    @if($order->is_budget)
                    	<input type="hidden" name="budget_id" value="{{ $order->id }}">
                    @endif
                    <input type="hidden" name="invoice_id" value="@if(isset($invoice)) {{ $invoice->id }} @endif">
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
                    	<label for="qty" class="col-lg-2 col-form-label price-fixed_">{!! ucfirst(trans('textos.cantidad')) !!} *</label>
						<div class="col-lg-4 price-fixed_">
							<x-input type="text" name="qty" class="text-right set-decimals" id="itemQty_" autocomplete="off" maxlength="10" required/>
						</div>

						{{-- Jornadas --}}
						<label for="days" class="col-lg-2 col-form-label price-fixed_">{!! ucfirst(trans('textos.jornadas')) !!}</label>
						<div class="col-lg-4 price-fixed_">
							<x-input type="text" name="days" class="text-right set-decimals" id="itemDays_" autocomplete="off" maxlength="10"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Precio --}}
						<label for="price" class="col-lg-2 col-form-label price-fixed_">{!! ucfirst(trans('textos.precio_unidad')) !!} </label>
						<div class="col-lg-4 price-fixed_">
							@php
							/**
							 * Condiciones:
							 * 1. Exclusivo proveedor o para cliente si el proveedor no tiene cuenta.
							 * 2. Para pedidos no facturados
							 */
							$readonly = (session('currentCompany') == $order->customer_id && $provider_account) || $order->invoiced? true:false;
							@endphp

							<x-input type="text" name="price" class="text-right set-decimals" autocomplete="off" maxlength="10" id="itemPrice_" value="0.00" group="right" groupvalue="{{ $currency->symbol }}" readonly="{{ $readonly }}"/>
						</div>

						{{-- IVA. Sólo en pedidos y facturas. En presupuestos no se tiene en cuenta este impuesto --}}
						@if($environment_ == 'orders' || $environment_ == 'invoices')
							<label for="iva" class="col-lg-2 col-form-label">IVA</label>
							<div class="col-lg-2">
								@if($readonly)
									<x-input type="text" name="iva" class="text-right set-decimals" id="itemIva_" autocomplete="off" maxlength="5" group="right" groupvalue="%" readonly="{{ $readonly }}"/>	

								@else
									{{-- 04/01/2023: se sustituye el input por un selecto con los tipos de IVA disponibles. --}}
									@php
									$iva_types = [];
									foreach($ivaTypes as $it){
										$iva_types[$it->iva] = $it->iva.'%'; 
									}
									@endphp

									<x-select name="iva" class="select2 iva0" id="itemIva_" label="iva_selec" :options="$iva_types"/>
								@endif								
							</div>

							{{-- Exento IVA --}}
							@if(!$readonly)
								<label for="without_iva" class="col-lg-1 col-form-label sinpadding">{{ trans('textos.iva_sin') }}</label>
								<div class="col-lg-1">
									<label class="custom-chk-rdb" rel="tooltip">
					                    <input type="checkbox" name="without_iva" class="withoutIva">
					                    <span class="chk" style="top: 5px;"></span>
					                </label>
					            </div>
					        @endif
						@endif

						{{-- Los inputs de precio por porcentaje se muestran en el siguiente bloque, por una cuestión de coherencia en la maquetación. --}}
					</div>

					@if($environment_ == 'invoices')
						<div class="form-group row">
							{{-- IRPF: se le pasa por valor el irpf asignado a la factura --}}
							<label for="irpf" class="col-lg-2 offset-md-9 col-form-label sinpadding">{{ trans('textos.irpf_descontar') }}</label>
							<div class="col-lg-1">
								<label class="custom-chk-rdb" rel="tooltip">
				                    <input type="checkbox" name="irpf" id="itemIrpf_" class="withoutIrpf">
				                    <span class="chk" style="top: 5px;"></span>
				                </label>
							</div>
						</div>	
					@endif

					@if(session('currentCompany') == $order->provider_id)
						<div class="form-group row">
							{{-- Descuento --}}
							@if(!$order->discount)
								<label for="discount" class="col-lg-2 col-form-label price-fixed_">{!! ucfirst(trans('textos.descuento')) !!} </label>
								<div class="col-lg-2 price-fixed_">
									<x-input type="text" name="discount" id="itemDiscount_" class="text-right set-decimals" autocomplete="off" maxlength="5" value="0.00" group="right" groupvalue="%"/>
								</div>

								@if($customer_settings && $customer_settings->discount)
									<div class="col-lg-4 price-fixed_">
										<button class="btn btn-warning btnApplyDiscount_" data-discount="{{ $customer_settings->discount }}">
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

							{{-- Precio por porcentaje --}}
							<label for="calculated_by_percent" class="col-lg-2 col-form-label price-by-percent_">{!! ucfirst(trans('textos.por_porcentaje')) !!} </label>
							<div class="col-lg-2 price-by-percent_">
								@php
								$readonly = session('currentCompany') == $order->customer_id? true:false;
								@endphp

								<x-input type="text" name="calculated_by_percent" class="text-right set-decimals" autocomplete="off" maxlength="8" id="itemCalculatedByPercent_" value="0.00" group="right" groupvalue="%" readonly="{{ $readonly }}" info="item-price-by-percent_"/>
							</div>
						</div>

						@if(isset($provider_cost_centers) && is_array($provider_cost_centers))
							<div class="form-group row">
								{{-- Centro de coste --}}
								<label for="provider_cost_center" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
								<div class="col-lg-4">
									<x-select name="provider_cost_center" class="select2" id="itemCostCenter_" label="centro_coste_selec" :options="$provider_cost_centers"/>
								</div>
							</div>
						@endif
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