<div class="modal fade modal-add-order" id="modal-add-order" tabindex="-1" role="dialog" aria-labelledby="addOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addOrder">{!! ucfirst(trans('textos.venta_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('project-orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="form-group row">
                        {{-- Ventas --}}
                        <label for="orders" class="col-12">{{ ucfirst(trans('textos.ventas')) }}*</label>
                        <div class="col-12">
                            {{-- @php
                            29/04/2024: se pasa a autocomplete:
                            $orders_ = [];

                            foreach($orders as $order){
                                $prefix = $order->order_num? $order->order_num:($order->order_num? $order->order_num:$order->id);
                                $sufix = $order->name? ' - '.App\Helpers\helpers::truncate($order->name, 40):'';
                                $orders_[$order->id] = $prefix.$sufix.' ('.$order->provider.')';
                            }
                            @endphp

                            <x-select name="orders[]" class="select2" :options="$orders_" multiple required/> --}}

                            <x-input type="text" name="order" id="search_order" placeholder="{!! ucfirst(trans('textos.pedido_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="orders[]" id="orderId_">
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>