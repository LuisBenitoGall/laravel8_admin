<div class="modal fade modal-add-product" id="modal-add-product" tabindex="-1" role="dialog" aria-labelledby="setCopy" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="setCopy">{!! ucfirst(trans('textos.producto_premium_add')) !!}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>			
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('tariff-products-excluded.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="tariff_id" value="{{ $tariff->id }}">

					<div class="form-group row">
						{{-- Productos --}}
						<label for="product" class="col-lg-12">{!! ucfirst(trans('textos.producto')) !!}*</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" id="product_search" name="product" placeholder="{!! trans('textos.articulo_escribe') !!}" required/>

                        	<x-input type="hidden" name="product_id" id="product_search_id"/>
 						</div>
					</div> 

					<div class="form-group row">
						{{-- Margen beneficio --}}
						<label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.margen')) !!}*</label>
						<div class="col-lg-8">
							<x-input type="text" name="discount" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="%" required/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>