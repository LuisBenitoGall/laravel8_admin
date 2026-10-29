<div class="modal fade modal-add-item" id="modal-add-item" tabindex="-1" role="dialog" aria-labelledby="newItem" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newItem">{!! ucfirst(trans('textos.item_nuevo')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
	            <form method="post" autocomplete="off" action="{{ route('briefing-template-items.store') }}" accept-charset="utf-8" role="form">
	                {{ csrf_field() }}
	                <input type="hidden" name="template_id" value="{{ $template->id }}">
	                    
                    <div class="form-group row">
                    	{{-- Item --}}
                    	<div class="col-md-10">
                    		<div class="row">
                    			<label for="name" class="col-lg-12">Item *</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
		                        </div>	
                    		</div>
                    	</div>
                        
                        {{-- Estado --}}
                        <div class="col-md-2">
                    		<div class="row">
                    			<label for="status" class="col-lg-12">{{ ucfirst(trans('textos.estado')) }}</label>
                    			<div class="col-lg-12">
                    				<label class="custom-chk-rdb" rel="tooltip">
            							<input type="checkbox" name="status" checked>
            							<span class="chk" style="top: 5px;"></span>
        							</label>
                    			</div>
                    		</div>
                    	</div>
                    </div>

                    <div class="form-group row">
                    	{{-- Bloque --}}
                    	<p class="text-warning col-12">Crea un bloque o selecciona uno para englobar los items en el mismo.</p>
                    	<label for="recid" class="col-lg-12">{{ ucfirst(trans('textos.bloque')) }}</label>
                        <div class="col-lg-12">
                    		<x-select name="recid" class="select2" label="bloque_selec" :options="$blocks"/>
                    	</div>
                    </div>

                    <div class="form-group row">
                    	{{-- Observaciones --}}
                        <label for="observations" class="col-lg-12">{{ ucfirst(trans('textos.observaciones')) }}</label>
                        <div class="col-lg-12">
                        	<x-input-textarea name="observations" class="editor"/>
                        </div>
                    </div>

					<div class="form-group row">
						{{-- Cantidad --}}
						<div class="col-md-6">
                    		<div class="row">
								<label for="expected_qty" class="col-12">{!! ucfirst(trans('textos.cantidad_prevista')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="expected_qty" class="text-right set-decimals" autocomplete="off" maxlength="20" info="briefing-template-item-quantity"/>
								</div>
							</div>
						</div>

						{{-- Unidad --}}
						<div class="col-md-6">
                    		<div class="row">
                    			<label for="time_unit" class="col-12">{!! ucfirst(trans('textos.unidad')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-select name="time_unit" class="select2" label="bloque_selec" :options="$time_units"/>
		                        </div>
		                    </div>
		                </div>
					</div> 
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
	        </div>
        </div>
    </div>
</div>