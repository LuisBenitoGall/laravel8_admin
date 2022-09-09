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
	                <form method="post" autocomplete="off" action="{{ route('receiving.store', $order->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                	@method('PUT')
	                    {{ csrf_field() }}

	                    <div class="form-group row">
							{{-- Fecha recepción --}}
							<label for="reception_date" class="col-12">{!! ucfirst(trans('textos.recepcion_fecha')) !!}</label>
							<div class="col-lg-8">
								<x-date-picker name="reception_date" :format="$languages[config('app.locale')][4]"/>
							</div>
						</div>

	                    <div class="form-group row">
	                    	{{-- Observaciones --}}
	                    	<label for="customer_observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
							<div class="col-12">
								<x-input-textarea name="customer_observations"/>
							</div>
						</div>

						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
	                </form>  
	            </div>
	        </div>
	    </div>
	</div>
@endif