<div class="modal fade modal-link-item" id="modal-link-item" tabindex="-1" role="dialog" aria-labelledby="linkItem" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="linkItem">{!! ucfirst(trans('textos.pedido_vincular')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('item-linked-orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="item_id" id="itemLinkedId">
                    <input type="hidden" name="order_origin" value="{{ $order->id }}">

                    <div class="form-group row">
                        {{-- Pedidos --}}
                        <label for="order" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedidos')) !!}*</label>
                        <div class="col-8">
                            <x-input type="text" name="order" id="search_order" placeholder="{!! ucfirst(trans('textos.pedido_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="order_id" id="orderId_">
                        </div>

                        <x-btn-submit name="submit_save" inline="true" label="{{ trans('textos.guardar') }}"/>
                    </div>
                </form>
                <br>
                <hr>

                <table class="modal-table" id="tblItemsLinked">
                    <thead>
                        <tr>
                            <th>{!! ucfirst(trans('textos.pedido')) !!}</th>
                            <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
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