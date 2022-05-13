<div class="modal fade modal-edit-product" id="modal-edit-product" tabindex="-1" role="dialog" aria-labelledby="editProduct" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editProduct">{!! ucfirst(trans('textos.producto_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('work-order-products.update') }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="wopId">

					<div class="form-group row">
						{{-- Producto --}}
                    	<label for="product_id" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!} *</label>
						<div class="col-8 col-lg-9">
							@php
							$products_ = [];
							foreach($products as $product){
								$products_[$product->id] = $product->name;
							}
							@endphp

							<x-select name="product_id" id="wopProduct" class="select2" label="producto_selec" :options="$products_" required/>
						</div>

						{{-- Ver catálogo --}}
						{{-- <div class="col-2 col-md-1">
							<button class="btn btn-warning btnSeeCatalogue" title="{!! ucfirst(trans('textos.catalogo')) !!}" data-toggle="modal" data-target="#modal-see-catalogue">
								<i class="fa fa-th"></i>
							</button>
						</div> --}}
					</div>

					<div class="form-group row">
						{{-- Cantidad --}}
                    	<label for="planned_qty" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cantidad_producir')) !!} *</label>
						<div class="col-lg-2">
							<x-input type="text" name="planned_qty" id="wopPlannedQty" class="text-right set-decimals" autocomplete="off" maxlength="10" value="1" required info="work-order-product-qty"/>
						</div>

						{{-- Generar número de lote --}}
						<label for="with_batch" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.lote_num_generar')) !!}</label>
						<div class="col-md-4 col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip">
					            <input type="checkbox" name="with_batch" id="productBatch">
					            <span class="chk" style="top: 5px;"></span>

					        	{!! App\Helpers\helpers::info('work-order-product-batch', true) !!}
					        </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
                    	<label for="observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-12">
							<x-input-textarea name="observations" id="wopObservations" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	var products_ = {!! json_encode($products) !!}

	// Limpiar formulario nuevo item.
	$('.btnAddItem').on('click', function(){
	    $('#frmAddItem').trigger("reset");
	});

	// Obtener gestión de lote del producto:
	$(document).on('click', '.btnEditWorkOrderProduct', function(e){
	    var id = $(this).data('id');
	    var product = $(this).data('product');
	    var plannedqty = $(this).data('plannedqty');
	    var observations = $(this).data('observations');

	    $('#wopId').val(id);
	    $('#wopPlannedQty').val(plannedqty);
	    $('#wopObservations').trumbowyg('html', observations);

	    var arr = [product];
        //Atención al modo de marcar como selected las opciones en un Select2:
        $('#wopProduct').val(arr);
        $('#wopProduct').trigger('change');
	});
});
</script>
@endpush