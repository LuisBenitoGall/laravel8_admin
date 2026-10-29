<div class="modal fade modal-edit-item" id="modal-edit-item" tabindex="-1" role="dialog" aria-labelledby="editItem" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editItem">{!! ucfirst(trans('textos.item_editar')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-form-items.update') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="ItemsId">
                    <input type="hidden" name="product_id" id="ItemsProductId">
                    <input type="hidden" name="form_id" value="{{ $form->id }}">

                    <div class="form-group row">
                    	{{-- Item --}}
                        <label for="name_" class="col-lg-12">Item *</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" id="ItemsName" maxlength="255"/>
                        	{{-- readonly="true" --}}
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Observaciones --}}
                        <label for="observations" class="col-lg-12">{{ ucfirst(trans('textos.observaciones')) }}</label>
                        <div class="col-lg-12">
                        	<x-input-textarea name="observations" id="ItemsObservations"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Precio. Viene dado de la tabla products --}}
                    	<div class="col-lg-6">
							<div class="row">
		                        <label for="price" class="col-lg-12">{!! ucfirst(trans('textos.precio')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="price_" id="ItemsPrice" class="text-right" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}" readonly="true" info="budget-form-item-price"/>

		                        	{{-- Guardamos el precio original para posible cambios --}}
		                        	<input type="hidden" id="ItemsOrigPrice">
		                        </div>
		                    </div>
		                </div>

		                {{-- Cálculo por porcentaje --}}
		                <div class="col-lg-6">
							<div class="row">
		                        <label for="calculated_by_percent" class="col-lg-12">{!! ucfirst(trans('textos.calculo_por_porcentaje')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="calculated_by_percent" id="ItemsCalculateByPercent" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-calculate-by-percent"/>
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
		                        	<x-input type="text" name="discount" class="text-right set-decimals" id="ItemsDiscount" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-discount"/>
								</div>
							</div>
						</div>

						{{-- Cantidad mínima --}}
						<div class="col-lg-6">
							<div class="row">
								<label for="qty_min" class="col-lg-12">{!! ucfirst(trans('textos.cantidad_min')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="qty_min" class="text-right set-decimals" id="ItemsQtyMin" autocomplete="off" maxlength="11" value="0" info="budget-form-item-qty-min"/>
		                        </div>
		                    </div>
		                </div>
		            </div>

		            <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>