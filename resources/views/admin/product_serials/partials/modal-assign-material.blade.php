<div class="modal fade modal-assign-material" id="modal-assign-material" tabindex="-1" role="dialog" aria-labelledby="assignMaterial" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="assignMaterial">{!! ucfirst(trans('textos.asignar_a_empleado')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('stocks.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="product_serial_id" id="productSerialId">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="movement" value="{{ $movement->id }}">
                    <input type="hidden" name="from_environment" value="serial">
                    <input type="hidden" name="qty" value="1">

					<div class="form-group row">
                        {{-- Usuario --}}
                        <label for="for_user" class="col-12">{{ ucfirst(trans('textos.usuario')) }}*</label>
                        <div class="col-12">
                            <x-select name="for_user" class="select2" label="usuario_selec" :options="$employees" required/>
                        </div>
                    </div>

                    {{-- Almacén origen --}}
                    <div class="form-group row">
                        <label for="store_id" class="col-12">{{ ucfirst(trans('textos.almacen_origen')) }}*</label>
                        <div class="col-12">
                            <x-select name="store_id" id="storeOrigin" class="select2" label="almacen_selec" :options="[]" required/>    
                        </div>
                    </div>

                    {{-- Lotes --}}
                    @if($product->batch)
                        <div class="form-group row">
                            <label for="batch_id" class="col-12">{{ ucfirst(trans('textos.lotes')) }}*</label>
                            <div class="col-12">  
                                <x-select name="batch_id" id="batchId" class="select2" label="lote_selec" :options="[]" required/> 
                            </div>
                        </div>  
                    @endif

                    {{-- Almacén destino --}}
                    @if(count($stores) == 1)
                        @php
                        foreach($stores as $i => $store){
                            $key = $i;
                            break;
                        }
                        @endphp
                        <input type="hidden" name="to_store" value="{{ $key }}">

                    @elseif(count($stores) > 1)
                        <div class="form-group row">
                            <label for="to_store" class="col-12">{{ ucfirst(trans('textos.almacen')) }}*</label>
                            <div class="col-12">
                                <x-select name="to_store" class="select2" label="almacen_selec" :options="$stores" required/>    
                            </div>
                        </div>
                    @endif

                    <div class="form-group row">
                        {{-- Observaciones --}}
                        <label for="observations" class="col-12">{{ ucfirst(trans('textos.observaciones')) }}</label>
                        <div class="col-12">
                            <x-input-textarea name="observations"/>     
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>