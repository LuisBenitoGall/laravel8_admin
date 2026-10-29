<div class="modal fade modal-edit-product" id="modal-edit-product" tabindex="-1" role="dialog" aria-labelledby="editProduct" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editProduct">{!! ucfirst(trans('textos.producto_premium_edit')) !!} <b></b></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>			
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('tariff-products-excluded.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="tariff_id" value="{{ $tariff->id }}">
					<input type="hidden" name="pe_id" id="peId">

					<div class="form-group row">
						{{-- Margen beneficio --}}
						<label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.margen')) !!}*</label>
						<div class="col-lg-8">
							<x-input type="text" name="discount" id="peDiscount" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="%" required/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>