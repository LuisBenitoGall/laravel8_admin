<div class="modal fade modal-reply-products" id="modal-reply-products" tabindex="-1" role="dialog" aria-labelledby="editLine" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editLine">{!! ucfirst(trans('textos.productos_premium_replicar')) !!} <b></b></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off"
				action="{{ route('tariffs.reply-products') }}" accept-charset="utf-8"
				enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="tariff_id" value="{{ $tariff->id }}">

					{{-- Tariffs --}}
					<div class="form-group row">
						<label for="tariffs"
						 class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.tarifas')) !!}*</label>
						<div class="col-lg-9">
							<x-select name="tariffs[]" class="select2" :options="$other_tariffs" multiple required/>
						</div>
					</div>

					<x-btn-submit name="submit_reply_products" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>