<div class="modal fade modal-new-component" id="modal-new-component" tabindex="-1" role="dialog" aria-labelledby="newComponent" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newComponent">{!! ucfirst(trans('textos.componente_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{{ trans('textos.produccion_orden_componente_texto') }}</p>

                <form method="post" autocomplete="off" action="{{ route('work-order-components.store') }}" id="frmAddComponent" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="work_order_id" value="{{ $work_order->id }}">

					<div class="form-group row">
						{{-- Componente --}}
                    	<label for="product_id" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.componente')) !!}*</label>
						<div class="col-10">
							<x-select name="product_id" id="componentId" class="select2" label="componente_selec" :options="$components" required/>

			                @if(count($components) == 0)
								<small class="text-danger">
									<strong>{!! ucfirst(trans('textos.catalogo_vacio')) !!}</strong>
									<a target="_blank" href="{{ route('products.create') }}">{!! ucfirst(trans('textos.producto_nuevo')) !!}</a>
								</small>
			                @endif
						</div>
					</div>

					<div class="form-group row">
						{{-- Cantidad requerida --}}
                    	<label for="supplied_qty" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cantidad_requerida')) !!}*</label>
						<div class="col-lg-2">
							<x-input type="text" name="supplied_qty" class="text-right set-decimals" autocomplete="off" maxlength="10" value="1" required info="work-order-product-required-qty"/>
						</div>

						{{-- Unidad --}}
						<label for="unit_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.unidad')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="unit_id" class="select2" label="unidad_selec" :options="$units" required/>
						</div>
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
	// Limpiar formulario nuevo item.
	$('.btnAddComponent').on('click', function(){
	    $('#frmAddComponent').trigger("reset");
	});
});
</script>
@endpush