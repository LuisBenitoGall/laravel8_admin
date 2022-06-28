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
                    	<label for="product_id" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!} *</label>
						<div class="col-8 col-lg-9">
							{{-- Selector de productos o input libre en función de la modalidad del formulario --}}
							@if($modality == config('constants.BUDGET_CATALOGUE_MODALITY_') || $environment_ == 'orders')

								@php
								$products_ = [];
								foreach($products as $product){
									$products_[$product->id] = $product->name;
								}
								@endphp

								<x-select name="product_id" id="productId" class="select2" label="producto_selec" :options="$products_" required/>

				                @if(count($products) == 0)
									<small class="text-danger">
										<strong>{!! ucfirst(trans('textos.catalogo_vacio')) !!}</strong>
										<a href="{{ route('products.create') }}">{!! ucfirst(trans('textos.producto_nuevo')) !!}</a>
									</small>
				                @endif

				            @elseif($modality == config('constants.BUDGET_BASIC_MODALITY_'))
				            	<x-input type="text" name="concept" autocomplete="off" maxlength="255" required/>	
				            @endif
						</div>

						{{-- Ver catálogo --}}
						@if($modality == config('constants.BUDGET_CATALOGUE_MODALITY_'))
							<div class="col-2 col-md-1">
								<button class="btn btn-warning btnSeeCatalogue" title="{!! ucfirst(trans('textos.catalogo')) !!}" data-toggle="modal" data-target="#modal-see-catalogue">
									<i class="fa fa-th"></i>
								</button>
							</div>
						@endif
					</div>

					<div class="form-group row">
						{{-- Cantidad --}}
                    	<label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!} *</label>
						<div class="col-lg-2">
							<x-input type="text" name="qty" class="text-right set-decimals" autocomplete="off" maxlength="10" value="1" required/>
						</div>

						{{-- Jornadas --}}
						<label for="days" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.jornadas')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="days" class="text-right set-decimals" autocomplete="off" maxlength="10"/>
						</div>

						{{-- Precio --}}
						<label for="price" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.precio_unidad')) !!} </label>
						<div class="col-lg-2">
							@php
							$readonly = session('currentCompany') == $order->customer_id? true:false;
							@endphp

							<x-input type="text" name="price" class="text-right set-decimals" autocomplete="off" maxlength="10" id="productPrice" value="0.00" group="right" groupvalue="{{ $currency->symbol }}" readonly="{{ $readonly }}"/>
						</div>
					</div>

					@if(session('currentCompany') == $order->provider_id)
						<div class="form-group row">
							{{-- Descuento --}}
							@if(!$order->discount)
								<label for="discount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descuento')) !!} </label>
								<div class="col-lg-2">
									<x-input type="text" name="discount" id="productDiscount" class="text-right set-decimals" autocomplete="off" maxlength="5" value="0.00" group="right" groupvalue="%"/>
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
									<x-input type="text" name="iva" class="text-right set-decimals" id="productIva" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
								</div>
							@endif
						</div>
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

	// Limpiar formulario nuevo item.
	$('.btnAddItem').on('click', function(){
	    $('#frmAddItem').trigger("reset");
	});

	// Obtener precio de producto.
	$('#productId').on('change', function(e){
	    var product_id = e.target.value;
	    //Buscamos precio:
	    products.forEach(function(i){
	        if(i.id == product_id){
	            $('#productPrice').val(i.price);
	            $('#productIva').val(i.iva);    
	        }
	    });
	});
});
</script>
@endpush