<div class="modal fade modal-edit-line" id="modal-edit-line" tabindex="-1" role="dialog" aria-labelledby="addLine" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="addLine">{!! ucfirst(trans('textos.linea_editar')) !!}</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>

<div class="modal-body">
<form method="post" autocomplete="off" action="{{ route('amortization-lines.update') }}"accept-charset="utf-8" enctype="multipart/form-data" role="form">
{{ csrf_field() }}
<input type="hidden" name="amortization_id" value="{{ $amortization->id }}">
<input type="hidden" name="id" id="lineId">

					<div class="form-group row">
						{{-- Importe --}}
						<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!} </label>
						<div class="col-lg-4 amount-fixed_">
							<x-input type="text" name="amount" id="lineAmount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}"/>
						</div>

						{{-- Porcentaje --}}
				<label for="percent" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.porcentaje')) !!}</label>
				<div class="col-lg-4">
				<x-input type="text" name="percent" id="linePercent" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="%"/>
				</div>
					</div>

<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
</form> 
</div>
</div>
</div>
</div>