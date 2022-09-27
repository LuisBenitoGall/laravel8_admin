<div class="modal fade modal-add-product" id="modal-add-product" tabindex="-1" role="dialog" aria-labelledby="addProduct" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addProduct">{!! ucfirst(trans('textos.producto_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('products.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="tutored" value="{{ $provider->id }}">
                    <input type="hidden" name="on_sale" value="1">
                    <input type="hidden" name="return" value="product-providers">
                    <input type="hidden" name="customer_product_id" value="{{ $product->id }}">

                    <div class="row">
                        <p class="col-12 text-warning mb-4">
                            <span class="btn btn-circle btn-circle-mini btn-warning">
                                <i class="fa fa-exclamation"></i>
                            </span>
                            {!! trans('textos.proveedor_producto_nuevo_texto') !!}
                        </p>
                    </div>

                    <div class="form-group row">
                        {{-- Producto --}}
                        <label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
                        <div class="col-lg-10">
                            <x-input type="text" name="name" autocomplete="off" required maxlength="250"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Precio: sólo para empresas tuteladas --}}
                        <label for="price" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.precio')) !!}</label>
                        <div class="col-lg-3">
                            <x-input type="text" name="price" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}"/>
                        </div>

                        {{-- Tipo --}}
                        <label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!} *</label>
                        <div class="col-lg-5" style="padding-top: 8px;">
                            <label class="custom-chk-rdb">
                                <input class="rdb-access" type="radio" name="type" value="p" required>
                                <span class="rdb"></span>
                                {!! ucfirst(trans('textos.producto')) !!}
                            </label>    

                            <label class="custom-chk-rdb ml-5">
                                <input class="rdb-access" type="radio" name="type" value="s" required>
                                <span class="rdb"></span>
                                {!! ucfirst(trans('textos.servicio')) !!}
                            </label>

                            <x-input-errors name="type"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>