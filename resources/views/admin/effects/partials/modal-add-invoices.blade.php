<div class="modal fade modal-add-invoices" id="modal-add-invoices" tabindex="-1" role="dialog" aria-labelledby="addInvoice" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addInvoice">{!! ucfirst(trans('textos.facturas')) !!}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('effects.set-invoices') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="effect_id" value="{{ $effect->id }}">

					<div class="form-group row">
						{{-- Facturas --}}
						<label for="invoices" class="col-2 col-form-label">{!! ucfirst(trans('textos.facturas')) !!}*</label>
						<div class="col-10">
							<x-select name="invoices[]" class="select2" multiple :options="$invoices_out" info="invoices" required/>	
						</div>
					</div>

					<x-btn-submit name="submit_add_invoices" label="{{ trans('textos.guardar') }}"/>
				</form> 
			</div>
		</div>
	</div>
</div>