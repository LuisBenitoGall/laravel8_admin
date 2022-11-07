<div class="modal fade modal-changes-round" id="modal-changes-round" tabindex="-1" role="dialog" aria-labelledby="ChangesRound" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="ChangesRound">{!! ucfirst(trans('textos.ronda_cambios')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<a href="#" class="btn btn-warning btnAddChangeRound">
            		<i class="fa fa-plus"></i>
            		{{ trans('textos.ronda_cambios_add') }}
            	</a>

            	<div id="addChangeRound" class="mt-4">
	        		<form method="post" autocomplete="off" action="{{ route('item-status.store') }}" id="frmSaveChangeRound" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="order_id" value="{{ $order->id }}">
	                    <input type="hidden" name="item_id" id="itemId">

	                    {{-- Comentarios proveedor --}}
	                    <div class="form-group row">
	                    	<div class="col-12">
	                            <label for="provider_observations">{!! trans('textos.comentarios_proveedor') !!}</label>
	                        </div>	

	                        <div class="col-12">
	                        	<x-input-textarea name="provider_observations" class="editor"/>
	                        </div>
	                    </div>

	                    {{-- Comentarios cliente --}}
	                    <div class="form-group row">
	                    	<div class="col-12">
	                            <label for="customer_observations">{!! trans('textos.comentarios_cliente') !!}</label>
	                        </div>	

	                        <div class="col-12">
	                        	<x-input-textarea name="customer_observations" class="editor"/>
	                        </div>
	                    </div>

	                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
	                </form>   
	            </div>
	            <hr>

                <table class="modal-table" id="tblChangesRound">
            		<thead>
            			<tr>
            				<th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
            				<th>{!! ucfirst(trans('textos.comentarios_proveedor')) !!}</th>
            				<th class="text-center">{!! ucfirst(trans('textos.comentarios_cliente')) !!}</th>
            				<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
            			</tr>
            		</thead>

            		<tbody>

            		</tbody>
            	</table>
            </div>
        </div>
    </div>
</div>