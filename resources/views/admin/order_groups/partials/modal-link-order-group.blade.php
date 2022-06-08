<div class="modal fade modal-link-order-group" id="modal-link-order-group" tabindex="-1" role="dialog" aria-labelledby="linkOrderGroup" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="linkOrderGroup">{!! ucfirst(trans('textos.agrupacion_vincular_')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	@if(count($order_groups))
	                <form method="post" autocomplete="off" action="{{ route('order-group-orders.add-order') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="order_id" value="{{ $order->id }}">

	                    <div class="form-group row">
	                        {{-- Agrupaciones --}}
	                        <label for="order_group" class="col-12">{!! ucfirst(trans('textos.agrupaciones')) !!}*</label>
	                        <div class="col-12">
	                        	@php
	                            $order_groups_ = [];
	                            @endphp

	                            @foreach($order_groups as $og)
	                                @php
	                                $num = $og->id;
	                                if($og->name){
	                                	$num .= ' - '.$og->name;
	                                }

	                                $order_groups_[$og->id] = $num;
	                                @endphp
	                            @endforeach

	                            <x-select name="order_group" class="select2" :options="$order_groups_" label="pedido_selec" required/>
	                        </div>
	                    </div>

		            	<x-btn-submit name="submit_add_order" label="{{ trans('textos.guardar') }}"/>
	                </form>   

	            @else
	            	<h4 class="text-center text-warning mb-4 mt-4">{{ trans('textos.no_agrupaciones') }}</h4>
	            @endif 
            </div>
        </div>
    </div>
</div>