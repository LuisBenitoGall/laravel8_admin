<div class="row mb-3">
    @php
    $setProduct = false;
    @endphp

    {{-- Column left --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-group row">
                    {{-- Productos --}}
                    <label for="product" class="col-12">{!! ucfirst(trans('textos.productos')) !!}*</label>
                    <div class="col-12" wire:ignore>
                        @if($products->count())
                            <select class="form-control select2" name="product" id="product">
                                <option value="">{{ trans('textos.producto_selec') }}</option>  

                                @foreach($products as $row){
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <p class="text-warning">{{ trans('textos.stock_no_productos') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Histórico productos seleccionados --}}
        @if($last_movements->count())
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12">
                            <h3 class="mb-3">{{ trans('textos.movimientos_ultimos') }}</h3>  
                        </div>     

                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Column right --}}
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                @if($product)
                    @php
                    $setProduct = true;
                    @endphp

                    <h3 class="mb-3">
                        {{ ucfirst(trans('textos.producto')) }} 
                        <u>{{ $product->name }}</u>
                    </h3>

                    {{-- Info --}}
                    <div>
                        {{-- Fecha alta --}}
                        <label class="mr-5">
                            {!! ucfirst(trans('textos.fecha_alta')) !!}: 
                            <b>
                                {{ $product->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
                            </b>
                        </label>

                        {{-- Referencia --}}
                        <label class="mr-5">
                            {!! ucfirst(trans('textos.referencia')) !!}: 
                            <b>
                                @if($product->ref)
                                    {{ $product->ref }}
                                @else
                                    {{ $product->manual_ref }}
                                @endif
                            </b>
                        </label>

                        {{-- Precio --}}
                        <label class="mr-5">
                            {!! ucfirst(trans('textos.precio')) !!}: 
                            <b>{{ $product->price }}{{ $currency->symbol }}</b>
                        </label>

                        {{-- IVA --}}
                        <label class="mr-5">
                            IVA: 
                            <b>{{ $product->iva }}%</b>
                        </label>

                        {{-- Gestión de lotes --}}
                        <label class="mr-5">
                            {!! ucfirst(trans('textos.lotes_gestion')) !!}:
                            @if($product->batch == 1)
                                <i class="fa fa-check text-success ml-1"></i>
                            @else
                                <i class="fa fa-ban text-danger ml-1"></i>
                            @endif
                        </label>

                        {{-- Acciones --}}
                        {{-- Editar --}}
                        <a target="_blank" href="{{ route('products.edit', $product) }}" class="btn btn-xs btn-info float-right ml-1" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}"><i class="fa fa-edit"></i></a>

                        {{-- Stock --}}
                        <a target="_blank" href="{{ route('stocks.show', $product) }}" class="btn btn-xs btn-info float-right" rel="tooltip" title="{{ ucfirst(trans('textos.movimientos_historico_ver')) }}"><i class="fa fa-tasks"></i></a>
                    </div>
                    <hr>

                    <h3 class="mb-2 mt-4">{{ ucfirst(trans('textos.stock_almacenes')) }}</h3>

                    {{-- Almacenes --}}
                    <table class="table" id="tblStores" wire:ignore>
                        <thead>
                            <tr>
                                <th>{{ ucfirst(trans('textos.almacen')) }}</th>
                                <th>{{ ucfirst(trans('textos.lote')) }}</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    {{-- Producto --}}
                                    <td>{{ $store->name }}</td>
                                    {{-- Lote --}}
                                    <td></td>
                                    {{-- Stock --}}
                                    <td class="text-right">
                                        @if($stock_by_store[$store->id] > 0)
                                            {{ $stock_by_store[$store->id] }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    {{-- Acciones --}}
                                    <td class="text-right">
                                        <a href="#" class="btn-xs btn btn-info btnSelectStore" wire:click="storeSelected({{ $store->id }})"  data-store="{{ $store->id }}" rel="tooltip" title="{{ ucfirst(trans('textos.almacen_selec')) }}">
                                            <i class="fa fa-filter"></i>
                                        </a>

                                        {{-- wire.click="storeSelected({{ $store->id }})" --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                {{-- Sin producto seleccionado --}}
                @else
                    <h2 class="text-center text-warning mb-3">
                        {{ trans('textos.producto_no_seleccionado') }}
                    </h2>
                @endif

                {{-- Formulario --}}
                <div class="mt-5 @if(!$store_selected) d-none @endif">
                    <form wire:submit.prevent="store" autocomplete="off" id="frm-stock" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}

                        <div class="form-group row">
                            {{-- Movimientos de stock --}}
                            <label for="movement" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.movimientos')) !!}*</label>
                            <div class="col-lg-5" wire:ignore>
                                <select class="form-control select2" name="movement" id="movement" wire:model.defer="movement_selected">
                                    <option value="">{{ trans('textos.movimiento_selec') }}</option>  
                                    @foreach($movements as $mov){
                                        <option value="{{ $mov->id }}">({{ strtoupper($mov->acronym) }}) {{ ucfirst($mov->name) }}</option>
                                    @endforeach
                                </select>    

                                @error('movement_selected') <span class="error">{{ $message }}</span> @enderror
                            </div>

                            {{-- Cantidad --}}
                            <label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!}</label>
                            <div class="col-lg-3" wire:ignore.defer>
                                <x-input type="text" name="qty" lw="qty" class="text-right" autocomplete="off" maxlength="9"/>

                                @error('qty') <span class="error">Debes indicar la cantidad</span> @enderror
                            </div>
                        </div>

                        @if($employees->count())
                            <div class="form-group row">
                                {{-- Empleados --}}
                                <label for="employee" class="col-md-3 col-form-label">{!! ucfirst(trans('textos.empleados')) !!}</label>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="employee" id="employee">
                                        <option value="">{{ trans('textos.empleado_selec') }}</option>  

                                        @foreach($employees as $row){
                                            <option value="{{ $row->id }}">{{ $row->name }}{{ $row->surname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">
                            {{-- Nº documento --}}
                            <label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
                            <div class="col-lg-3" wire:ignore.defer>
                                <x-input type="text" name="doc_num" lw="doc_num"/>
                            </div>

                            {{-- Documento --}}
                            <label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
                            <div class="col-lg-5" wire:ignore.defer>
                                <x-input-file name="doc" lw="doc"/>

                                {!! App\Helpers\helpers::info('stock-doc') !!}

                                @if($errors->has('doc'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('doc') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- Observaciones --}}
                            <label for="observations" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                            <div class="col-md-10" wire:ignore.defer>
                                <x-input-textarea name="observations" lw="observations"/>
                            </div>
                        </div>

                        <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Selectores:
    //document.addEventListener('livewire:load', function(){
    document.addEventListener('DOMContentLoaded', function(){
        $('#product').select2(); 

        // Selección producto:
        $('#product').on('change', function(e){   
            @this.emit('productSelected', this.value);
        });

        // Selección almacén:
        $(document).on('click', '.btnSelectStore', function(){
            $('#tblStores td').removeClass('selected');
            var store = $(this).data('store');
            console.log(store);
            @this.set('store_selected', store);

            $(this).closest('tr').find('td').addClass('selected');
        });

        // Selección movimiento stock:
        $('#movement').on('change', function(e){   
            @this.set('movement_selected', this.value);
        });



        

        




    });


    $(document).ready(function(){
        




        window.initSelect2 = () => {

        }

        initSelect2();
        window.livewire.on('select2_lw', ()=>{
            initSelect2();
        });   

        


    });



    </script>
</div>