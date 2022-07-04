<div class="modal fade modal-add-item" id="modal-add-item" tabindex="-1" role="dialog" aria-labelledby="newItem" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newItem">{!! ucfirst(trans('textos.item_nuevo')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	@if($products->count())
	                <form method="post" autocomplete="off" action="{{ route('budget-form-items.store') }}" accept-charset="utf-8" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="form_id" value="{{ $form->id }}">
	                    
	                    <div class="form-group row">
	                    	{{-- Item --}}
	                        <label for="product_id" class="col-lg-12">Item *</label>
	                        <div class="col-lg-12">
	                        	@php
	                        	$products_ = [];
	                        	@endphp

	                        	@foreach($products as $product)
	                        		@php
	                        		$products_[$product->id] = $product->name;
	                        		@endphp
	                        	@endforeach

	                        	<x-select name="product_id" id="product_id" class="select2" label="producto_selec" required :options="$products_" />
	                        </div>
	                    </div>

		                <div class="form-group row">
	                    	{{-- Bloque --}}
	                        <label for="recid" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!} *</label>
	                        <div class="col-lg-12">
	                        	@php
	                        	$blocks_ = [];
	                        	@endphp

	                        	@foreach($blocks as $block)
	                        		@php
	                        		$blocks_[$block->id] = $block->name;
	                        		@endphp
	                        	@endforeach

	                        	<x-select name="recid" class="select2" label="bloque_selec" required :options="$blocks_" />
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                    	{{-- Observaciones --}}
	                        <label for="observations" class="col-lg-12">{{ ucfirst(trans('textos.observaciones')) }}</label>
	                        <div class="col-lg-12">
	                        	<x-input-textarea name="observations"/>
	                        </div>
	                    </div>

						<div class="form-group row">
							{{-- Precio. Viene dado de la tabla products --}}
							<div class="col-lg-6">
								<div class="row">
									<label for="price" class="col-lg-12">{!! ucfirst(trans('textos.precio')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="price_" id="itemPrice" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}" info="budget-form-item-price"/>
									</div>	
								</div>
							</div>

							{{-- Cálculo por porcentaje --}}
							<div class="col-lg-6">
								<div class="row">
			                        <label for="calculated_by_percent" class="col-lg-12">{!! ucfirst(trans('textos.calculo_por_porcentaje')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="calculated_by_percent" id="calculated_by_percent" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-calculate-by-percent"/>
									</div>
								</div>
							</div>
						</div> 

						<div class="form-group row">
							{{-- Descuento --}}
							<div class="col-lg-6">
								<div class="row">
			                        <label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.descuento')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="discount" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-discount"/>
									</div>
								</div>
							</div>

							{{-- Cantidad mínima --}}
							<div class="col-lg-6">
								<div class="row">
									<label for="qty_min" class="col-lg-12">{!! ucfirst(trans('textos.cantidad_min')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="qty_min" class="text-right set-decimals" autocomplete="off" maxlength="11" value="0" info="budget-form-item-qty-min"/>
			                        </div>
			                    </div>
			                </div>
			            </div>
						
						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
	                </form>    

                @else
                	<p class="text-center text-warning mt-4">
						<b>{!! ucfirst(trans('textos.catalogo_vacio')) !!}</b> 
						<a href="{{ route('products.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.catalogo')) !!}</a>
					</p>
	            @endif
            </div>
        </div>
    </div>
</div>