<div class="modal fade modal-add-orders" id="modal-add-orders" tabindex="-1" role="dialog" aria-labelledby="newOrder" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newOrder">{!! ucfirst(trans('textos.pedidos_agregar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	@if(count($orders_off))
	                <form method="post" autocomplete="off" action="{{ route('order-group-orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="group_id" value="{{ $group->id }}">

	                    <div class="form-group row">
	                        {{-- Pedidos --}}
	                        <label for="orders" class="col-12">{!! ucfirst(trans('textos.pedidos')) !!}*</label>
	                        <div class="col-12">
	                        	@php
	                            $orders_off_ = [];
	                            @endphp

	                            @foreach($orders_off as $oo)
	                                @php
	                                $num = $oo->order_num? $oo->order_num:$oo->order_id;
	                                if($oo->name){
	                                	$num .= ' - '.$oo->name;
	                                }

	                                $orders_off_[$oo->order_id] = $num;
	                                @endphp
	                            @endforeach

	                            <x-select name="orders[]" class="select2" :options="$orders_off_" required multiple/>
	                        </div>
	                    </div>

		            	<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
	                </form>   

	            @else
	            	<h4 class="text-center text-warning mb-4 mt-4">{{ trans('textos.no_pedidos_agrupables') }}</h4>
	            @endif 
            </div>
        </div>
    </div>
</div>