<div class="modal fade modal-new-product" id="modal-new-product" tabindex="-1" role="dialog" aria-labelledby="newLine" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newLine">{!! ucfirst(trans('textos.producto_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{{ trans('textos.produccion_orden_texto') }}</p>

                <form method="post" autocomplete="off" action="{{ route('work-order-products.store') }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="work_order_id" value="{{ $work_order->id }}">

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

							<x-select name="product_id" id="productId" class="select2" label="producto_selec" :options="$products_" required/>

			                @if(count($products) == 0)
								<small class="text-danger">
									<strong>{!! ucfirst(trans('textos.catalogo_vacio')) !!}</strong>
									<a href="{{ route('products.create') }}">{!! ucfirst(trans('textos.producto_nuevo')) !!}</a>
								</small>
			                @endif
						</div>

						{{-- Ver catálogo --}}
						<div class="col-2 col-md-1">
							<button class="btn btn-warning btnSeeCatalogue" title="{!! ucfirst(trans('textos.catalogo')) !!}" data-toggle="modal" data-target="#modal-see-catalogue">
								<i class="fa fa-th"></i>
							</button>
						</div>
					</div>

					<div class="form-group row">
						{{-- Cantidad --}}
                    	<label for="planned_qty" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cantidad_producir')) !!} *</label>
						<div class="col-lg-2">
							<x-input type="text" name="planned_qty" class="text-right set-decimals" autocomplete="off" maxlength="10" value="1" required info="work-order-product-qty"/>
						</div>

						{{-- Almacén --}}
						<label for="store" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.almacen')) !!} *</label>
						<div class="col-lg-4">
							<x-select name="store" class="select2" label="almacen_selec" :options="$stores" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Generar número de lote --}}
						<label for="with_batch" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.lote_num_generar')) !!}</label>
						<div class="col-md-4 col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip">
					            <input type="checkbox" name="with_batch" id="productBatch">
					            <span class="chk" style="top: 5px;"></span>

					        	{!! App\Helpers\Helpers::info('work-order-product-batch', true) !!}
					        </label>
						</div>	
					
						{{-- Patrones --}}
						@if(count($batch_patterns))
							<label for="pattern_id" class="col-lg-3 col-form-label withBatch">{!! ucfirst(trans('textos.patron')) !!}</label>
							<div class="col-md-4 withBatch">
								<x-select name="pattern_id" class="select2" label="patron_selec" :options="$batch_patterns" info="batch-pattern"/>
							</div>
						@endif
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
                    	<label for="observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-12">
							<x-input-textarea name="observations" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	var products = {!! json_encode($products) !!}

	//Opciones de lote van ocultas por defecto.
	$('.withBatch').hide();

	// Limpiar formulario nuevo item.
	$('.btnAddItem').on('click', function(){
	    $('#frmAddItem').trigger("reset");
	});

	// Obtener gestión de lote del producto:
	$('#productId').on('change', function(e){
		$('.withBatch').fadeOut();
		$('#productBatch').prop('checked', false);
	    var product_id = e.target.value;
	    //Buscamos precio:
	    products.forEach(function(i){
	        if(i.id == product_id && i.batch){
	            $('#productBatch').prop('checked', true);
	            $('.withBatch').fadeIn();
	        }
	    });
	});

	// Mostrar / ocultar patrones de lote:
	$('#productBatch').on('change', function(){
		if($(this).is(':checked')){
			$('.withBatch').fadeIn();	
		}else{
			$('.withBatch').fadeOut();
		}
	});

});
</script>
@endpush