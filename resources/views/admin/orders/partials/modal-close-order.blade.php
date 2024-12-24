<div class="modal fade modal-close-order" id="modal-close-order" tabindex="-1" role="dialog" aria-labelledby="closeOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="closeOrder">{!! ucfirst(trans('textos.pedido_cerrar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('orders.close') }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <p class="text-warning">
                        {{ ucfirst(trans('textos.pedido_cerrar_texto')) }}
                    </p>

                    <div class="form-group row">
                        {{-- Estado --}}
                        <label for="status" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.estado')) !!} *</label>
                        <div class="col-lg-9">
                            @php
                            $close_status = App\Helpers\helpers::orderStatus();
                            $valid_status[] = trans('textos.estado_selec');
                            foreach($close_status as $ics => $cs){
                                if(in_array($ics, config('constants.ORDER_STATUS_CLOSED_'))){
                                    $valid_status[$ics] = $cs;    
                                }
                            }
                            @endphp
                            <x-select name="status" class="select2" :options="$valid_status"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_close_order" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>