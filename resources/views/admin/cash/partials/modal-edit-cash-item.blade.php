<div class="modal fade modal-edit-cash-item" id="modal-edit-cash-item" tabindex="-1" role="dialog" aria-labelledby="editLine" aria-hidden="true">
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
                    <input type="hidden" name="id" id="productId_">
                    <input type="hidden" name="cash_id" value="{{ $cash->id }}">
                    <input type="hidden" name="environment" value="cash">

                    <div class="form-group row">
                        {{-- Producto --}}
                        <label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
                        <div class="col-lg-10">
                            <x-input type="text" name="concept" autocomplete="off" id="productConcept_" maxlength="150" required/> 
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cantidad --}}
                        <label for="qty" class="col-lg-2 col-form-label price-fixed_">{!! ucfirst(trans('textos.cantidad')) !!} *</label>
                        <div class="col-lg-4 price-fixed_">
                            <x-input type="text" name="qty" class="text-right set-decimals" id="productQty_" autocomplete="off" maxlength="10" required/>
                        </div>

                        {{-- Precio --}}
                        <label for="price" class="col-lg-2 col-form-label price-fixed_">{!! ucfirst(trans('textos.precio_unidad')) !!} </label>
                        <div class="col-lg-4 price-fixed_">
                            <x-input type="text" name="price" class="text-right set-decimals" autocomplete="off" maxlength="10" id="productPrice_" value="0.00" group="right" groupvalue="{{ $currency->symbol }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Descuento --}}
                        <label for="discount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descuento')) !!}</label>
                        <div class="col-lg-4 position-relative">
                            <x-input type="text" name="discount" class="text-right set-decimals" id="productDiscount_" value="" autocomplete="off" maxlength="6" group="right" groupvalue="%"/>
                        </div>

                        {{-- IVA --}}
                        <label for="iva" class="col-lg-2 col-form-label">IVA</label>
                        <div class="col-lg-4 position-relative">
                            <x-input type="text" name="iva" class="text-right set-decimals" id="productIva_" value="" autocomplete="off" maxlength="6" group="right" groupvalue="%"/>    
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Observaciones --}}
                        <label for="observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                        <div class="col-12">
                            <x-input-textarea name="observations" class="editor" id="productObservations_"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update_items" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>