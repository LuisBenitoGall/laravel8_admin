<div class="modal fade modal-seat-show" id="modal-seat-show" tabindex="-1" role="dialog" aria-labelledby="showSeat" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="showSeat">{!! ucfirst(trans('textos.asiento')) !!} <b></b></h4>

				<a target="_blank" class="btnGoSeat ml-5 btn btn-xs btn-info" href="">{{ trans('textos.asiento_ir') }}</a>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="form-group row">
					{{-- Asiento --}}
					<label class="col-lg-6" id="seatId">{!! ucfirst(trans('textos.asiento')) !!}: <b></b></label>
					{{-- Fecha --}}
					<label class="col-lg-6" id="seatDate">{!! ucfirst(trans('textos.fecha')) !!}: <b></b></label>
					{{-- Descripción: --}}
					<label class="col-12" id="seatDescription">{!! ucfirst(trans('textos.descripcion')) !!}: <b></b></label>
					{{-- Diario --}}
					<label class="col-lg-6" id="seatDiary">{!! ucfirst(trans('textos.diario')) !!}: <b></b></label>
					{{-- Concepto --}}
					<label class="col-lg-6" id="seatConcept">{!! ucfirst(trans('textos.concepto')) !!}: <b></b></label>
				</div>

				<div class="form-group row">
					<div class="col-12">
						<table class="table" id="tblSeatLinesDetail">
							<thead>
								<tr>
									<th>{{ trans('textos.cuenta_contable') }}</th>
									<th  class="text-center">{{ trans('textos.debe') }}</th>
									<th  class="text-center">{{ trans('textos.haber') }}</th>
									<th  class="text-center">{{ trans('textos.factura') }}</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>					
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>