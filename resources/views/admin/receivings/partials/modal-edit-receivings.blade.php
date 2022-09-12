@if($order->customer_id == session('currentCompany'))
	<div class="modal fade modal-edit-receivings" id="modal-edit-receivings" tabindex="-1" role="dialog" aria-labelledby="editReceivings" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="editReceivings">{!! ucfirst(trans('textos.recepcion_editar')) !!} <b></b></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	                <form method="post" autocomplete="off" action="{{ route('receiving.update', $order->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                	@method('PUT')
	                    {{ csrf_field() }}
	                    <input type="hidden" id="editReceivingId" name="id">

	                    <div class="form-group row">
							{{-- Fecha recepción --}}
							<label for="estimated_reception_date" class="col-12">{!! ucfirst(trans('textos.recepcion_fecha')) !!}</label>
							<div class="col-lg-8">
								<x-date-picker name="estimated_reception_date" id="editEstimatedReceptionDate" :format="$languages[config('app.locale')][4]"/>
							</div>
							<small class="col-12 text-warning" id="estimatedReceptionDateText">{{ trans('textos.recepcion_no_modificable') }}</small>
						</div>

	                    <div class="form-group row">
	                    	{{-- Observaciones --}}
	                    	<label for="customer_observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
							<div class="col-12">
								<x-input-textarea id="editCustomerObservations" name="customer_observations"/>
							</div>
						</div>

						<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
	                </form>  
	            </div>
	        </div>
	    </div>
	</div>
@endif