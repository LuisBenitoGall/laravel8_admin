<div class="modal fade modal-new-line" id="modal-new-line" tabindex="-1" role="dialog" aria-labelledby="newLine" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newLine">{!! ucfirst(trans('textos.linea_nueva')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('items.store') }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="environment" value="{{ $environment_ }}">
                    @if($environment_ == 'invoices')
                    	<input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    @endif

                    @if($errors->has('order_id'))
                        <span class="invalid-feedback" role="alert">
                          	<strong>{{ $errors->first('order_id') }}</strong>
                        </span>
                    @endif
                    @if($errors->has('budget_id'))
                        <span class="invalid-feedback" role="alert">
                          	<strong>{{ $errors->first('budget_id') }}</strong>
                        </span>
                    @endif
                    @if($errors->has('order_id'))
                        <span class="invalid-feedback" role="alert">
                          	<strong>{{ $errors->first('order_id') }}</strong>
                        </span>
                    @endif

					<div class="form-group row">
						{{-- Producto --}}
                    	<label for="product_id" class="col-md-2 col-form-label">
                    		{!! ucfirst(trans('textos.producto')) !!} 
                    		@if(session('currentCompany') == $order->provider_id) * @endif
                    	</label>
						<div class="col-8 col-lg-9">
							{{-- Selector de productos o input libre en función de la modalidad del formulario --}}
							@if($modality == config('constants.BUDGET_CATALOGUE_MODALITY_') || $environment_ == 'orders' || $environment_ == 'invoices')

								@php
								$products_ = [];
								foreach($products as $product){
									$products_[$product->id] = $product->name;
								}

								$required = session('currentCompany') == $order->provider_id? true:false;
								@endphp

								<x-select name="product_id" id="productId" class="select2" label="producto_selec" :options="$products_" />

								{{-- Crear nuevo producto - condiciones: 
									1. Catálogo sin productos.
									2. Opción para el proveedor.
									...
								--}}
				                @if(count($products) == 0 && session('currentCompany') == $order->provider_id)
									<small class="text-danger">
										<strong>{!! ucfirst(trans('textos.catalogo_vacio')) !!}</strong>
										<a href="{{ route('products.create') }}">{!! ucfirst(trans('textos.producto_nuevo')) !!}</a>
									</small>
				                @endif

				            @elseif($modality == config('constants.BUDGET_BASIC_MODALITY_') && session('currentCompany') == $order->provider_id)
				            	<x-input type="text" name="concept" autocomplete="off" maxlength="255" required/>	
				            @endif
						</div>

						{{-- Ver catálogo --}}
						@if(!$modality || $modality == config('constants.BUDGET_CATALOGUE_MODALITY_'))
							<div class="col-2 col-md-1">
								<button class="btn btn-warning btnSeeCatalogue" title="{!! ucfirst(trans('textos.catalogo')) !!}" data-toggle="modal" data-target="#modal-see-catalogue">
									<i class="fa fa-th"></i>
								</button>
							</div>
						@endif
					</div>

					{{-- Línea manual. Para pedidos de compra si el producto no consta en el catálogo. Su precio es 0 --}}
					@if(session('currentCompany') == $order->customer_id)
						<div class="form-group row">
							<label for="concept" class="col-md-2 col-form-label sinpadding-left">{!! ucfirst(trans('textos.producto_manual')) !!}</label>	
							<div class="col-md-10">
								<x-input type="text" name="concept" class="search_purchase_product" id="productManual" autocomplete="off" info="order-product-manual"/>
							</div>
						</div>

						<input type="hidden" name="purchase_product_id" id="purchaseProductId">
					@endif

					{{-- Correspondencia de referencia de producto para compras. --}}
					@if(session('currentCompany') == $order->customer_id)
						<div class="form-group row" id="customerRef">
							<div class="col-md-10 offset-md-2">
								<p class="text-warning" style="margin-bottom: 5px;">{{ trans('textos.correspondencia_ref_texto') }}:</p>
								<span></span>
							</div>
						</div>
					@endif

					<div class="form-group row">
						{{-- Cantidad --}}
                    	<label for="qty" class="col-lg-2 col-form-label price-fixed">{!! ucfirst(trans('textos.cantidad')) !!} *</label>
						<div class="col-lg-4 price-fixed">
							<x-input type="text" name="qty" class="text-right set-decimals" autocomplete="off" maxlength="10" value="1" required/>
						</div>

						{{-- Jornadas --}}
						<label for="days" class="col-lg-2 col-form-label price-fixed">{!! ucfirst(trans('textos.jornadas')) !!}</label>
						<div class="col-lg-4 price-fixed">
							<x-input type="text" name="days" class="text-right set-decimals" autocomplete="off" maxlength="10"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Precio. Puede ser introducido y modificado por el cliente si el proveedor no gestiona su cuenta de empresa --}}
						<label for="price" class="col-lg-2 col-form-label price-fixed">{!! ucfirst(trans('textos.precio_unidad')) !!} </label>
						<div class="col-lg-4 price-fixed">
							@php
							$readonly = session('currentCompany') == $order->customer_id && $provider_account? true:false;
							@endphp

							<x-input type="text" name="price" class="text-right set-decimals" autocomplete="off" maxlength="10" id="productPrice" value="0.00" group="right" groupvalue="{{ $currency->symbol }}" readonly="{{ $readonly }}"/>
						</div>

						{{-- IVA. Sólo en pedidos. En presupuestos no se tiene en cuenta este impuesto --}}
						@if($environment_ == 'orders' || $environment_ == 'invoices')
							<label for="iva" class="col-lg-2 col-form-label">IVA</label>
							<div class="col-lg-2">
								<x-input type="text" name="iva" class="text-right set-decimals iva0" id="productIva" autocomplete="off" maxlength="5" group="right" groupvalue="%" readonly="{{ $readonly }}" value="{{ config('constants.IVA_DEFAULT_') }}"/>
							</div>

							{{-- Exento IVA --}}
							<label for="without_iva" class="col-lg-1 col-form-label sinpadding">{{ trans('textos.iva_sin') }}</label>
							<div class="col-lg-1">
								<label class="custom-chk-rdb" rel="tooltip">
				                    <input type="checkbox" name="without_iva" class="withoutIva">
				                    <span class="chk" style="top: 5px;"></span>
				                </label>
				            </div>
						@endif

						{{-- Los inputs de precio por porcentaje se muestran en el siguiente bloque, por una cuestión de coherencia en la maquetación. --}}
					</div>

					@if($environment_ == 'invoices' && $invoice->irpf > 0)
						<div class="form-group row">
							{{-- IRPF: se le pasa por valor el irpf asignado a la factura --}}
							<label for="irpf" class="col-lg-2 offset-md-9 col-form-label sinpadding">{{ trans('textos.irpf_descontar') }}</label>
							<div class="col-lg-1">
								<label class="custom-chk-rdb" rel="tooltip">
				                    <input type="checkbox" name="irpf" class="withoutIrpf" value="{{ $invoice->irpf }}">
				                    <span class="chk" style="top: 5px;"></span>
				                </label>
							</div>
						</div>	
					@endif

					@if(session('currentCompany') == $order->provider_id)
						<div class="form-group row">
							{{-- Descuento --}}
							@if(!$order->discount)
								<label for="discount" class="col-lg-2 col-form-label price-fixed">{!! ucfirst(trans('textos.descuento')) !!} </label>
								<div class="col-lg-2 price-fixed">
									<x-input type="text" name="discount" id="productDiscount" class="text-right set-decimals" autocomplete="off" maxlength="5" value="0.00" group="right" groupvalue="%"/>
								</div>

								@if($customer_settings && $customer_settings->discount)
									<div class="col-lg-4 price-fixed">
										<button class="btn btn-warning btnApplyDiscount" data-discount="{{ $customer_settings->discount }}">
											<i class="fa fa-percent"></i>
											{!! trans('textos.descuento_clientes_aplicar') !!} ({{ $customer_settings->discount }}%)
										</button>

										{!! App\Helpers\helpers::info('order-default-discount-to-line', true) !!}
									</div>
								@endif
							@else
								<div class="col-lg-8">
									<p class="text-warning">{!! trans('textos.descuento_pedido_aplicado_aviso') !!}</p>
								</div>
							@endif

							{{-- Precio por porcentaje --}}
							<label for="calculated_by_percent" class="col-lg-2 col-form-label price-by-percent">{!! ucfirst(trans('textos.por_porcentaje')) !!} </label>
							<div class="col-lg-2 price-by-percent">
								@php
								$readonly = session('currentCompany') == $order->customer_id? true:false;
								@endphp

								<x-input type="text" name="calculated_by_percent" class="text-right set-decimals" autocomplete="off" maxlength="8" id="productPriceByPercent" value="0.00" group="right" groupvalue="%" readonly="{{ $readonly }}" info="item-price-by-percent"/>
							</div>
						</div>

						@if(isset($provider_cost_centers) && is_array($provider_cost_centers))
							<div class="form-group row">
								{{-- Centro de coste --}}
								<label for="provider_cost_center" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
								<div class="col-lg-4">
									<x-select name="provider_cost_center" class="select2" id="productCostCenter" label="centro_coste_selec" :options="$provider_cost_centers"/>
								</div>
							</div>
						@endif
					@endif

					<div class="form-group row">
						{{-- Observaciones --}}
                    	<label for="observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-12">
							<x-input-textarea name="observations" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_store_items" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	var products = {!! json_encode($products) !!}
	var perspective = $('#myPerspective').val();

	// Limpiar formulario nuevo item.
	$('.btnAddItem').on('click', function(){
	    $('#frmAddItem').trigger("reset");
	});

	// Obtener precio de producto + correspondencia de referencia (compras).
	$('#productId').on('change', function(e){
	    var product_id = e.target.value;
	    $('#customerRef').hide();
	    //Buscamos precio:
	    products.forEach(function(i){
	        if(i.id == product_id){
	        	console.log(i);
	        	//Centro de coste:
	        	var arr = [i.cost_center_id];
	        	$('#productCostCenter').val(arr);
	        	$('#productCostCenter').trigger('change');

	        	//Precio por porcentaje:	
	        	if(i.price == '0' && i.calculated_by_percent > 0){
	        		$('#productPriceByPercent').val(i.calculated_by_percent);
	        		$('#productPrice').val('');
	        		$('.price-fixed').fadeOut(400, function(){
	        			$('.price-by-percent').fadeIn();	
	        		});

	        	//Precio fijo:
	        	}else{
	        		//Comprobamos si el cliente tiene precio personalizado:
	        		var precio = i.priceForCustomer? i.priceForCustomer:i.price;
	        		$('#productPrice').val(precio);
	        		$('#productPriceByPercent').val('');
	        		$('.price-by-percent').fadeOut(400, function(){
	        			$('.price-fixed').fadeIn();	
	        		});	        
	        	}
	            
	            $('#productIva').val(i.iva);    
	        }
	    });

	    $('#productManual').val('');

	    //Buscamos correspondencia de referencia del producto seleccionado:
	    if(perspective == 'purchase'){
	    	$.get("/admin/product-providers/"+product_id+"/show", function(response, state){
	    		if(response && response.id){
	    			$('#customerRef span').html('<b>'+response.name+'</b><a target="_blank" href="/admin/products/'+response.id+'/show" class="ml-3" title="'+txtVer+'"><i class="fa fa-eye"></i></a>');
	    			$('#customerRef').fadeIn();
	    		}
	    	});
	    }
	});
	
	$('#productManual').on('change', function(){
		var valor = $(this).val();
		var arr = [''];
        $('#productId').val(arr);
        $('#productId').trigger('change');

		$('#productPrice').val('0.00');
		$(this).val(valor);
	});
});
</script>
@endpush