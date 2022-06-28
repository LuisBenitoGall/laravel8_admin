@if($order->provider_id == session('currentCompany') && count(session('companies')) > 1 && !$order->order_id)
	<div class="modal fade modal-set-provider" id="modal-set-provider" tabindex="-1" role="dialog" aria-labelledby="setProvider" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="setProvider">{!! ucfirst(trans('textos.proveedor_cambio')) !!}</h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	            	<form method="post" autocomplete="off" action="{{ route('budgets.set-provider', $order) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="budget_id" value="{{ $order->id }}">

	                    <div class="form-group row">
	                    	{{-- Empresas --}}
							<label for="company_id" class="col-lg-12">{!! ucfirst(trans('textos.empresas')) !!} *</label>
	                        <div class="col-lg-12">
	                        	@php
	                        	$companies_ = [];
	                        	foreach(session('companies') as $c){
	                        		//Omitimos el cliente:
	                        		if($c->id != $order->customer_id){
	                        			$companies_[$c->id] = $c->name;	
	                        		}
	                        	}
	                        	@endphp

								<x-select name="company_id" class="select2" label="empresa_selec" :options="$companies_" required/>
							</div>
						</div>

	                    <x-btn-submit name="submit-set-provider" label="{{ trans('textos.guardar') }}"/>
	                </form>  
	            </div>
	        </div>
	    </div>
	</div>
@endif