<div class="modal fade modal-seat-edit" id="modal-seat-edit" tabindex="-1" role="dialog" aria-labelledby="showSeat" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editSeat">{!! ucfirst(trans('textos.asiento_editar')) !!} <b></b></h4>

				<a target="_blank" class="btnGoSeat ml-5 btn btn-xs btn-info" href="">{{ trans('textos.asiento_ir') }}</a>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" id="frmEditSeat" autocomplete="off" action="" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="accounting_account_extracted" value="@if($accounting_account){{ $accounting_account->id }}@endif">

					<div class="form-group row mt-4">
						<div class="col-1 text-right"><b>#</b></div>
						<div class="col-md-5 text-center"><b>{{ trans('textos.cuenta_contable') }}</b></div>
						<div class="col-md-2 text-center"><b>{{ ucfirst(trans('textos.tipo')) }}</b></div>
						<div class="col-md-2 text-center"><b>{{ trans('textos.debe') }}</b></div>
						<div class="col-md-2 text-center"><b>{{ trans('textos.haber') }}</b></div>
					</div>		

					<div id="editSeatLines">

					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>