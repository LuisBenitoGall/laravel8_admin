<div class="modal fade modal-add-remittance" id="modal-add-remittance" tabindex="-1" role="dialog" aria-labelledby="addRemittance" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addRemittance">{!! ucfirst(trans('textos.remesas')) !!}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('effects.set-remittance') }}"accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="effect_id" id="effectId">

					<div class="form-group row">
						{{-- Remesas --}}
						<label for="remittance" class="col-2 col-form-label">{!! ucfirst(trans('textos.remesas')) !!}*</label>
						<div class="col-10">
							<x-select name="remittance" class="select2" label="remesa_selecciona" :options="$remittances" info="remittances" required/>	
						</div>
					</div>

					<x-btn-submit name="submit_remittance" label="{{ trans('textos.guardar') }}"/>
				</form> 
			</div>
		</div>
	</div>
</div>