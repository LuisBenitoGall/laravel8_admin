<div class="modal fade modal-set-block" id="modal-set-block" tabindex="-1" role="dialog" aria-labelledby="setBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setBlock">{!! ucfirst(trans('textos.presupuestos_lineas_sin_bloque')) !!}</h4>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{!! trans('textos.presupuestos_lineas_sin_bloque_aviso') !!}</p>

            	<form method="post" autocomplete="off" action="{{ route('budgets.set-block') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="form-group row">
                        {{-- Bloques --}}
						<label for="block_id" class="col-lg-12">{!! ucfirst(trans('textos.bloques')) !!}*</label>
                        <div class="col-lg-12">
                            @php
                            $blocks_ = [];
                            foreach($blocks as $blk){
                                $blocks_[$blk->id] = $blk->name;
                            }
                            @endphp

                            <x-select name="block_id" class="select2" label="bloque_selec" :options="$blocks_" required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_set_block" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>