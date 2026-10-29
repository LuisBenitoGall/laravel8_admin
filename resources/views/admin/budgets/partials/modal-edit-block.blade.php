<div class="modal fade modal-edit-block" id="modal-edit-block" tabindex="-1" role="dialog" aria-labelledby="editBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editBlock">{!! ucfirst(trans('textos.bloque_editar_nombre')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('budget-form-items.update-block') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="bfi_id" id="bfiId">

                    <div class="form-group row">
                    	{{-- Bloque --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!} *</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" id="bfiName" autocomplete="off" maxlength="100" required />
                        </div>
                    </div>

                    {{-- Centros de coste --}}
                    @if($provider_cost_centers->count())
                        <div class="form-group row">
                            <label for="cost_center" class="col-lg-12">{!! ucfirst(trans('textos.centro_coste')) !!}*</label>
                            <div class="col-lg-12">
                                @php
                                $cost_centers = [];
                                foreach($provider_cost_centers as $pcc){
                                    $cost_centers[$pcc->id] = $pcc->name;
                                }
                                @endphp

                                <x-select name="cost_center" class="select2" id="bfiCostCenter" label="centro_coste_selec" :options="$cost_centers" required/>
                            </div>
                        </div>
                    @endif

                    <x-btn-submit name="submit_update_block" label="{{ trans('textos.guardar') }}"/>
                </form>  
            </div>
        </div>
    </div>
</div>