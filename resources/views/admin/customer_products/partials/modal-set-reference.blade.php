<div class="modal fade modal-set-reference" id="modal-set-reference" tabindex="-1" role="dialog" aria-labelledby="configDayType" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="configDayType">{!! ucfirst(trans('textos.correspondencia_ref_prov')) !!} <strong></strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{!! trans('textos.correspondencia_ref_prov_texto') !!}</p>

                <form method="post" autocomplete="off" action="{{ route('product-providers.store-from-provider') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    <input type="hidden" name="provider_product_id" id="providerProductId">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        {{-- Producto --}}
                        <label for="customer_product_id" class="col-md-2 col-form-label">
                            {!! ucfirst(trans('textos.producto')) !!} 
                        </label>
                        <div class="col-8 col-lg-9">
                            <x-select name="customer_product_id" class="select2" label="producto_selec" :options="$session_products" />

                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>