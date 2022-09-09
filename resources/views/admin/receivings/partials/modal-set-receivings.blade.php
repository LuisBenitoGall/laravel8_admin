@if($order->customer_id == session('currentCompany'))
	<div class="modal fade modal-set-receivings" id="modal-set-receivings" tabindex="-1" role="dialog" aria-labelledby="setReceivings" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="setReceivings">{!! ucfirst(trans('textos.recepcion_pedido')) !!} <b></b></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	                <form method="post" autocomplete="off" action="{{ route('receiving.store', $order->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                	@method('PUT')
	                    {{ csrf_field() }}

	                    <div class="row">
	                    	<p class="col-12 text-warning mb-4">
	                    		<span class="btn btn-circle btn-circle-mini btn-warning">
	                    			<i class="fa fa-exclamation"></i>
	                    		</span>
	                    		{!! trans('textos.recepcion_pedido_texto') !!}
	                    	</p>
	                    </div>

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

	                <hr>

	                {{-- Relación de recepciones --}}
	                @if(count($receivings))
	                	<table class="modal-table" id="tblOrderReceivings">
		            		<thead>
		            			<tr>
		            				<th>{{ ucfirst(trans('textos.fecha')) }}</th>
		            				<th class="text-center">{{ ucfirst(trans('textos.estado')) }}</th>
		            				<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
		            			</tr>
		            		</thead>

		            		<tbody>
		            			@foreach($receivings as $rcv)
		            				<tr>
		            					<td>
		            						{!! \Carbon\Carbon::parse($rcv->reception_date)->format($languages[session()->get('locale')][4]) !!} 
		            					</td>
		            					<td class="text-center">
		            						@if($rcv->status == 1)
		            							<i class="fa fa-check text-success" rel="tooltip" title="{{ trans('textos.recepcionado') }}"></i>
		            						@else
		            							<i class="fa fa-exclamation text-warning" rel="tooltip" title="{{ trans('textos.pendiente') }}"></i>
		            						@endif
		            					</td>
		            					<td class="text-right">
		            						{{-- Editar --}}
		            						<a href="" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ trans('textos.editar') }}" data-toggle="modal" data-target="#modal-edit-receivings"><i class="fa fa-edit"></i></a>
		            					</td>
		            				</tr>
		            			@endforeach		
		            		</tbody>
		            	</table>	
	                @endif
	            </div>
	        </div>
	    </div>
	</div>
@endif