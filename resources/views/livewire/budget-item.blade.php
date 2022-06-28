@php
//Estados de líneas de presupuesto sin importe
$statusBudgetLinesNoPrice = App\Helpers\helpers::statusBudgetLinesWithoutPrice();

//Comprobamos que las líneas de presupuesto tipo formulario pertenezcan a algún bloque:
$setBlock = 1;

//Bloqueo de modificación de items:
$permitted = !$order || ($order && !$order->invoiced && !$order->parent)? true:false;
@endphp

<div class="row">
    {{-- Bloques --}}
    @if($blocks)
        <div class="col-md-12 col-lg-3 col-xl-2 mb-4">
            {{-- Agregar nuevo bloque: sólo si el pedido no está facturado 100% --}}
            {{-- @if(!$order || $order->percentage_invoiced < 100) --}}
                <a href="#" class="btn btn-warning ml-5 float-right no-spinner" data-toggle="modal" data-target="#modal-add-block">
                    <i class="fa fa-plus"></i>
                    {!! ucfirst(trans('textos.bloque_nuevo')) !!}
                </a>
            {{-- @endif --}}
            <div class="clearfix"></div>

            @if($blocks->count())
                <div id="accordion" class="mt-3" role="tablist">
                    @php
                    $i = 1;
                    $totalAmount = 0;
                    @endphp

                    @foreach($blocks as $block)
                        @php
                        $blockAmount = 0;
                        @endphp

                        <div class="card">
                            <div class="card-header" role="tab" id="heading{{ $i }}">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="mb-0">
                                            <a data-toggle="collapse" href="#collapse{{ $i }}" aria-expanded="false" aria-controls="collapse{{ $i }}" class="toggle-arrow collapsed">
                                                <i class="fa fa-caret-down"></i>
                                                {{ ucfirst($block->name) }}
                                            </a>
                                        </h4>
                                    </div>  
                                </div>
                            </div>

                            {{-- Items --}}
                            <div id="collapse{{ $i }}" class="collapse" role="tabpanel" aria-labelledby="heading{{ $i }}" data-parent="#accordion" style="">
                                <div class="card-body">
                                    @php
                                    $items = $block->getItems($block->id);
                                    $k = 1;
                                    @endphp

                                    {{-- Items de la plantilla del formulario --}}
                                    @foreach($items as $item)
                                        <div class="budget-item @if($k%2 == 0) bg-item @endif" data-block="{{ $i }}">
                                            <div class="row">
                                                <div class="col-12 only-vertical-centered">
                                                    {{-- Item --}}
                                                    <b class="concept">{{ ucfirst($item->name) }}</b>   
                                                </div>

                                                <div class="col-10 pt-2">
                                                    {{-- Precio --}}
                                                    <span style="line-height: 30px;">{{ $item->price }}{{ $currency->symbol }}</span>

                                                    {{-- Descuento --}}
                                                    @if($item->discount)
                                                        <span class="ml-5">{{ $item->discount }}%</span>
                                                    @endif

                                                    {{-- Agregar este item --}}
                                                    @php
                                                    $qty_min = $item->qty_min && $item->qty_min > 0? $item->qty_min:1;
                                                    @endphp

                                                    @if($permitted)
                                                        {{-- <button class="btn btn-warning btn-circle btn-circle-mini addBudgetItem no-spinner float-right mr-3" rel="tooltip" data-blockid="{{ $block->id }}" data-id="{{ $item->id }}" data-price="{{ $item->price }}" data-concept="{{ $item->name }}" data-discount="{{ $item->discount }}" data-observations="{{ $item->observations }}" data-qtymin="{{ $qty_min }}" data-bypercent="{{ $item->calculated_by_percent }}" title="{!! trans('textos.presupuesto_add') !!}">
                                                            <i class="fa fa-plus"></i>
                                                        </button> --}}

                                                        
                                                        <button class="btn btn-warning btn-circle btn-circle-mini addBudgetItem float-right" rel="tooltip" title="{!! trans('textos.presupuesto_add') !!}" wire:click="addItem({{ $item }})">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>                          
                        </div>

                        <input type="hidden" class="blockSubtotal" data-bst="{{ $i }}" value="{{ $blockAmount }}">

                        @php
                        $i++;
                        @endphp
                    @endforeach 
                </div>

            @else
                <h4 class="text-center text-warning mb-5 mt-5">{!! trans('textos.items_no_existe') !!}</h4>
            @endif
        </div>
    @endif

    {{-- Items --}}
    <div class="col-md-12 col-lg-9 col-xl-10">
        {{-- Agregar item - condiciones:
            1. Pedido simple no vinculado a ninguna agrupación.
            2. Que no esté facturado
        --}}
        @if($permitted)
            <button class="btn btn-warning no-spinner btnAddBudgetItem @if($form_on) opened @endif" 
                id="newBudgetItem" 
                wire:click="resetForm()"
            >
                @if($form_on)
                    <i class="fa fa-minus"></i>
                    {!! ucfirst(trans('textos.cancelar')) !!}
                @else
                    <i class="fa fa-plus"></i>
                    {!! ucfirst(trans('textos.agregar_linea')) !!}    
                @endif
            </button>
        @else
            <div style="height: 35px;"></div>
        @endif

        {{-- Formulario --}}
        <div wire:ignore.self class="new-budget-item mt-3">
            <form wire:submit.prevent="store" autocomplete="off" id="frm-save-item" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <input type="hidden" name="item_id" id="newItemId" wire:model.defer="item_id">

                <div class="form-group row">
                    {{-- Concepto --}}
                    <label for="concept" class="col-3 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.concepto')) !!}*</label>
                    <div class="col-9 col-sm-10 col-xl-5 mb-3 mb-xl-0">
                        <textarea name="concept" class="form-control" id="newItemConcept" required wire:model.defer="concept"></textarea>
                    </div>

                    {{-- Bloque --}}
                    <label for="block" class="col-3 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.bloque')) !!}*</label>
                    <div class="col-9 col-sm-10 col-xl-5" wire:ignore>
                        <select name="block" id="newItemBlock" class="form-control select2_lw" required wire:model.defer="block">
                            <option value="">{{ trans('textos.bloque_selec') }}</option>  
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}">{{ $block->name }}</option>    
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    {{-- Cantidad --}}
                    <label for="qty" class="col-4 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.cantidad')) !!}</label>
                    <div class="col-8 col-sm-4 col-lg-3 col-xl-2 mb-3">
                        <input type="number" class="form-control text-center" name="qty" min="0" step="0.01" value="1" id="newItemQty" wire:model.defer="qty" @if($field_readonly) readonly @endif>
                    </div>

                    {{-- Precio: en caso de ser 0, se incluye opción para indicar el motivo de dejarlo sin precio --}}
                    <label for="price" class="col-4 col-sm-2 col-xl-1 col-form-label sinpadding-right" id="lblPrice">{!! ucfirst(trans('textos.precio')) !!}</label>
                    <div class="col-8 col-sm-4 col-lg-3 col-xl-2 mb-3">
                        <div class="input-group">
                            {{-- Botón selector opciones de precio 0 --}}
                            @if(!$field_readonly)
                                <div class="input-group-prepend">
                                    <span class="input-group-addon">
                                        <i class="fa fa-caret-down text-success btnOptionsWithoutPrice" rel="tooltip" title="{{ ucfirst(trans('textos.abrir_selector')) }}"></i>
                                    </span>
                                </div>
                            @endif

                            <input type="text" name="price" class="form-control text-right set-decimals" id="newItemPrice" autocomplete="off" maxlength="8" wire:model.defer="price" @if($field_readonly) readonly @endif>
                            <div class="input-group-append">
                                <span class="input-group-addon">@if($field_readonly) % @else {{ $currency->symbol }} @endif</span>
                            </div>
                        </div>

                        {{-- Selector opciones de precio 0 --}}
                        <div class="optionsWithoutPrice">
                            <small class="text-warning">{{ trans('textos.precio_0_texto') }}</small>
                            <select name="line_no_price" id="lineNoPrice" class="form-control">
                                <option value="" selected disabled>{{ trans('textos.opcion_selec') }}</option>
                                @foreach($statusBudgetLinesNoPrice as $idx => $bl)
                                    @php
                                    $selected = isset($itemSelected) && isset($itemSelected->line_no_price)? 'selected':'';
                                    @endphp
                                    <option value="{{ $idx }}" {{ $selected }}>{{ $bl }}</option>
                                @endforeach
                            </select>

                            <span class="close-line-status">
                                <i class="fa fa-times" rel="tooltip" title="{{ ucfirst(trans('textos.cerrar')) }}"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Descuento: sólo se aplica si no hay descuento neto sobre el presupuesto --}}
                    @if(!$shared->discount)
                        <label for="discount" class="col-4 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.descuento')) !!}</label>
                        <div class="col-8 col-sm-4 col-lg-3 col-xl-2 mb-3">
                            <div class="input-group">
                                <input type="text" name="discount" class="form-control text-right set-decimals" id="newItemDiscount" autocomplete="off" maxlength="5" wire:model.defer="discount" @if($field_readonly) readonly @endif>
                                <div class="input-group-append">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Jornadas --}}
                    <label for="days" class="col-4 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.jornadas')) !!}</label>
                    <div class="col-8 col-sm-4 col-lg-3 col-xl-2 mb-3">
                        <input type="number" class="form-control text-center set-decimals itemDays" name="days" min="0" step="1" value="0" id="newItemDays" wire:model.defer="days" maxlength="2" @if($field_readonly) readonly @endif>
                    </div>

                    {{-- Imprimir --}}
                    <label for="print" class="col-4 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.imprimir')) !!}</label>
                    <div class="col-8 col-sm-1 col-xl-1 mb-3">
                        <label class="custom-chk-rdb">
                            <input type="checkbox" wire:model.defer="print" @if($print) checked @endif>
                            <span class="chk" style="top: 5px;"></span>
                        </label>  
                    </div>
                </div>

                <div class="form-group row row-eq-height">
                    {{-- Observaciones --}}
                    <label for="observations" class="col-4 col-sm-2 col-xl-1 col-form-label sinpadding-right">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                    <div class="col-9 col-sm-10 col-xl-11">
                        <textarea class="form-control editor" name="observations" id="newItemObservations" wire:model.defer="observations"></textarea>  
                    </div>
                </div>

                <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
            </form>
        </div>

        {{-- Items en presupuesto --}}
        @if(count($items_selected))
            <div class="budget-items mt-3">
                {{-- Agrupamos por bloque --}}
                @foreach($blocks as $block)
                    {{-- Componemos previamente el html --}}
                    @php
                    $block_title = 0;
                    $first_item = true;
                    $k = 1;     //Contador para la ordenación de items;
                    
                    //Calculamos el importe total a partir de las líneas con precio fijo, sin considerar las líneas con importe porcentual
                    $amountWithoutPercentItems = 0;
                    $totalAmountWithoutDiscounts = 0;   //Importe sin considerar los descuentos aplicados en los items.
                    @endphp

                    @foreach($items_selected as $is)
                        {{-- Seteamos $setBlock si la línea NO pertenece a ningún bloque de formulario --}}
                        @if(!$is->budget_form_block_id)
                            @php
                            $setBlock = '0';
                            @endphp
                        @endif

                        {{-- Indicamos orden si no viene declarado --}}
                        @if(!$is->budget_order)
                            @php
                            !$is->budget_order = $k;
                            @endphp
                        @endif

                        {{-- Líneas de template de presupuesto --}}
                        @if($block->id == $is->budget_form_block_id)
                            @php
                            $block_title++;
                            @endphp

                            {{-- Mostrando título e importe del bloque --}}
                            @if($block_title == 1)
                                <div class="block" data-block="{{ $block->id }}" id="block-{{ $block->id }}">
                                    <h3>
                                        {{ ucfirst($block->name) }}
                                        <span class="block-amount ml-4 text-warning">{{ $amount_by_blocks[$block->id] }}{{ $currency->symbol }}</span>
                                    </h3>
                                </div>
                            @endif

                            {{-- Líneas --}}
                            <div class="block-item" id="item-{{ $is->id }}">
                                <h5>
                                    {{-- Imprimir: --}}
                                    @php
                                    $print = $is->print == 1? 'text-success':'text-danger';
                                    $title = $is->print == 1? ucfirst(trans('textos.se_imprime')):ucfirst(trans('textos.imprimir_no'));
                                    @endphp

                                    <i class="fa fa-print mr-2 itemPrint {{ $print }}" rel="tooltip" title="{{ $title }}"></i>

                                    {{-- Concepto: --}}
                                    {{ $is->concept }}
                                </h5>

                                <div class="row">
                                    {{-- Controles --}}
                                    <div class="col-1 sinpadding-right">
                                        {{-- Orden --}}
                                        <span class="order-item mr-2" rel="tooltip" title="{{ ucfirst(trans('textos.ordenar')) }}">
                                            {{-- Subir --}}
                                            @if(!$first_item)
                                                <a class="up" wire:click="itemUp({{ $is }})"><i class="fa fa-caret-up"></i></a>
                                            @endif
                                            {{-- Bajar --}}
                                            <a class="down" wire:click="itemDown({{ $is }})"><i class="fa fa-caret-down"></i></a>
                                        </span>

                                        @if($permitted)
                                            {{-- Editar --}}
                                            @if(!$is->calculated_by_percent)
                                                <a class="text-warning mr-2 btnEditBudgetItem" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" wire:click="editItem({{ $is }})"><i class="fa fa-edit"></i></a>
                                            @endif

                                            {{-- Eliminar: --}}
                                            <form method="POST" action="{{ route('items.destroy', $is) }}" class="frm-inline mr-2" role="form" accept-charset="UTF-8">
                                                <input name="_method" type="hidden" value="DELETE">
                                                <input type="hidden" name="return" value="budgets">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn-empty text-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.linea_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.presupuesto_linea_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
                                            </form>

                                            {{-- Duplicar: 
                                                .1 Sólo items con precio estable, no porcentual.    
                                            --}}
                                            @if(!$is->calculated_by_percent)
                                                <a href="{{ route('items.copy', $is) }}" class="text-warning mr-2 btn-confirm" data-msg="{{ trans('textos.presupuesto_linea_duplicar_confirm') }}" rel="tooltip" title="{{ ucfirst(trans('textos.duplicar')) }}"><i class="fa fa-copy"></i></a>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Cantidad: --}}
                                    <div class="col-2 sinpadding-right">   
                                        <b>{{ trans('textos.cantidad') }}:</b> 
                                        @if($is->calculated_by_percent)
                                            <i class="fa fa-ban text-danger"></i>
                                        @else
                                            <span class="mr-5 itemQty">{{ $is->qty }}</span>    
                                        @endif
                                    </div>

                                    {{-- Precio: --}}
                                    <div class="col-2 sinpadding-right">   
                                        <b>{{ trans('textos.precio') }}:</b> 
                                        <span class="mr-5">
                                            {{-- Sin precio --}}
                                            @if($is->item_no_price)
                                                {{ $is->item_no_price }}

                                            {{-- Con precio --}}
                                            @else
                                                @if($is->calculated_by_percent)
                                                    {{ trans('textos.porcentual') }}    
                                                @else
                                                    {{ $is->price }}{{ $currency->symbol }}    
                                                @endif
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Descuento: sólo se aplica si no existe descuento neto sobre el presupuesto --}}
                                    @if(!$shared->discount)
                                        <div class="col-2 sinpadding-right">   
                                            <b>{{ ucfirst(trans('textos.descuento')) }}:</b> 
                                            @if($is->calculated_by_percent)
                                                <i class="fa fa-ban text-danger"></i>
                                            @else
                                                <span class="mr-5">@if($is->discount > 0) {{ $is->discount }}% @endif</span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Jornadas: --}}
                                    <div class="col-2 sinpadding-right">   
                                        <b>{{ ucfirst(trans('textos.jornadas')) }}:</b> 
                                        @if($is->calculated_by_percent)
                                            <i class="fa fa-ban text-danger"></i>
                                        @else
                                            <span class="mr-5">{{ $is->days }}</span>
                                        @endif
                                    </div>

                                    {{-- Subtotal: --}}
                                    <div class="col-2 sinpadding-right">   
                                        @php
                                        //En función de si se calcula porcentualmente sobre el total o no:
                                        $subtotal = $is->calculated_by_percent? App\Helpers\Helpers::itemAmountPercentage($is->calculated_by_percent, $amount_without_percentage_items):App\Helpers\Helpers::itemAmount($is);
                                        @endphp
                                        <b>{{ ucfirst(trans('textos.subtotal')) }}:</b> <span class="mr-5">{{ $subtotal }}{{ $currency->symbol }}</span>
                                    </div>
                                </div>

                                @if($is->observations)
                                    <div class="row">
                                        {{-- Observaciones: --}}
                                        <div class="col-12 mt-2">
                                            <i>{!! $is->observations !!}</i>
                                        </div>
                                    </div>
                                @endif
                                <hr>
                            </div>

                            @php
                            $block_title++;
                            $first_item = false;
                            $k++;
                            @endphp
                        @endif
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>

    <script>
    //Sentencia para poder utilizar JQuery con Livewire:
    document.addEventListener('DOMContentLoaded', function(){
        //Modal por si existen líneas que no pertenecen a ningún bloque de formulario:
        var set_block = '{!! $setBlock !!}';
        if(set_block == '0'){
            $('#modal-set-block').modal({
                backdrop: 'static', 
                keyboard: false,
                toggle: true
            });
        }

        //Nuevo item manual:
        $('.btnAddBudgetItem').on('click', function(){
            if($('.btnAddBudgetItem').hasClass('opened')){
                $('.new-budget-item').slideUp();   
                $('.btnAddBudgetItem').removeClass('opened').html('<i class="fa fa-plus"></i> '+txtLineaAdd);
                @this.set('form_on', false);
            }else{
                $('.new-budget-item').slideDown();   
                //Modo para esconder el formulario:
                $('.btnAddBudgetItem').addClass('opened').html('<i class="fa fa-minus"></i> '+txtCancelar);
                @this.set('form_on', true);
            }
        });

        //Acción del formulario al editar o agregar línea desde bloque:
        $('.addBudgetItem, .btnEditBudgetItem').on('click', function(){
            $('.new-budget-item').slideDown();   
            //Modo para esconder el formulario:
            $('.btnAddBudgetItem').addClass('opened').html('<i class="fa fa-minus"></i> '+txtCancelar);   
        });

        //Select2:
        $('.select2_lw').select2();
        $('.select2_lw').on('change', function(e){
            var block = $(this).select2('val');
            @this.set('block', block);
        });

        //Modificar valor dinámicamente en Select2:
        window.initSelect2 = () => {
            jQuery("#newItemBlock").select2({
                minimumResultsForSearch: 2,
                allowClear: false,
                width: '100%'
            });
        }
        initSelect2();
        window.livewire.on('select2_lw', ()=>{
            initSelect2();
        });

        //Selector opciones item sin precio:
        $('.btnOptionsWithoutPrice').on('click', function(){
            $('.optionsWithoutPrice').fadeIn();
        });
        $('.close-line-status').on('click', function(){
            $('.optionsWithoutPrice').fadeOut();
        });
        $('#lineNoPrice').on('change', function(){
            var text = $('#lineNoPrice option:selected').text();
            @this.set('price', text);
        });
    });
    </script>
</div>
