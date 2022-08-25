<div class="modal fade modal-add-purchase" id="modal-add-purchase" tabindex="-1" role="dialog" aria-labelledby="addPurchase" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addPurchase">{!! ucfirst(trans('textos.compra_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('project-purchases.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="form-group row">
                        {{-- Compras --}}
                        <label for="orders" class="col-12">{{ ucfirst(trans('textos.compras')) }}*</label>
                        <div class="col-12">
                            @php
                            $purchases_ = [];

                            foreach($purchases as $purchase){
                                $prefix = $purchase->purchase_num? $purchase->purchase_num:($purchase->order_num? $purchase->order_num:$purchase->id);
                                $sufix = $purchase->name? ' - '.App\Helpers\helpers::truncate($purchase->name, 40):'';
                                $purchases_[$purchase->id] = $prefix.$sufix.' ('.$purchase->provider.')';
                            }
                            @endphp

                            <x-select name="orders[]" class="select2" :options="$purchases_" multiple required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>