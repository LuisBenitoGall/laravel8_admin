<div class="modal fade" id="modal-add-order" tabindex="-1" role="dialog" aria-labelledby="addOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addOrder">{!! ucfirst(trans('textos.pedido_adjuntar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

			<div class="modal-body">
				<p class="text-warning">{!! ucfirst(trans('textos.pedido_adjuntar_aviso')) !!}</p>

				<form method="post" autocomplete="off" action="{{ route('delivery-orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">
                    <input type="hidden" name="return" value="delivery">
					
                    <div class="form-group row">
                    	{{-- Pedidos --}}
                        <label for="orders" class="col-lg-12">{!! ucfirst(trans('textos.pedidos')) !!}</label>
                        <div class="col-lg-12">
                        	@if($orders->count())
	                        	@php
								$orders_ = [];
								foreach($orders as $order){
									//Sólo pedidos no incluidos en la entrega.
									if(!in_array($order->id, $arr_delivery_orders)){
										$num = $order->order_num? $order->order_num:($order->order_old_num? $order->order_old_num:$order->id);

										$orders_[$order->id] = $num.' - '.$order->name;
									}
								}
								@endphp

								<x-select name="orders[]" class="select2" :options="$orders_" multiple/>
							@else
								<p class="text-center text-warning mt-4">{{ trans('textos.usuarios_no_mas') }}</p>
							@endif
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
			</div>
        </div>
    </div>
</div>