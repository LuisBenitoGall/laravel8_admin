@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
        {{-- Catálogo --}}
        <a target="_blank" href="{{ route('products.index') }}" class="btn btn-info">
            <i class="fa fa-gift"></i>
            {!! ucfirst(trans('textos.catalogo')) !!}
        </a>

		{{-- Nuevo producto - condiciones 
			1. Disponer de permisos de creación de producto.
			...
        --}}
		@can('products.create')
    		<a target="_blank" href="{{ route('products.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

{{-- Componente stocks --}}
{{-- 27/09/2022: se omite por ahora la opción de componente de Livewire, pues existen dificultades con los select2 dependientes entre sí que por ahora no sé como resolver --}}
{{-- @livewire('stocks') --}}

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
                                    @php
                                    $selected = $product_id && $row->id == $product_id? 'selected':'';
                                    @endphp
                                    <option value="{{ $row->id }}" {{ $selected }}>{{ $row->name }}</option>
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

                        <div class="col-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('textos.fecha') }}</th>
                                        <th>{{ ucfirst(trans('textos.producto')) }}</th>
                                        <th class="text-center">{{ trans('textos.cantidad') }}</th>
                                        <th class="text-center"></th>
                                    </tr>    
                                </thead>

                                <tbody>
                                    @foreach($last_movements as $lm)
                                        <tr>
                                            <td>{{ $lm->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}</td>
                                            <td>{{ $lm->name }}</td>
                                            <td class="text-right">
                                                @if($lm->operation == 'r') - @endif {{ $lm->qty }}
                                            </td>
                                            {{-- Acciones --}}
                                            <td class="text-right">
                                                {{-- Utilizar mismo producto --}}
                                                <a href="{{ route('stocks.index', $lm->id) }}" class="text-warning" rel="tooltip" title="{{ trans('textos.movimiento_nuevo') }}">
                                                    <i class="fa fa-redo"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
            	<div class="productForm" @if($errors->any()) style="display: block;" @endif>
                    @php
                    $setProduct = true;
                    @endphp

                    <h3 class="mb-3" id="productTitle">
                        {{ ucfirst(trans('textos.producto')) }} 
                        <u></u>
                    </h3>

                    {{-- Info --}}
                    <div>
                        {{-- Fecha alta --}}
                        <label class="mr-5" id="productAlta">
                            {!! ucfirst(trans('textos.fecha_alta')) !!}: 
                            <b>
                                {{-- {{ $product->created_at->format($languages[session()->get('locale')][4].' H:i:s') }} --}}
                            </b>
                        </label>

                        {{-- Referencia --}}
                        <label class="mr-5" id="productRef">
                            {!! ucfirst(trans('textos.referencia')) !!}: 
                            <b></b>
                        </label>

                        {{-- Precio --}}
                        <label class="mr-5" id="productPrice">
                            {!! ucfirst(trans('textos.precio')) !!}: 
                            <b></b>
                        </label>

                        {{-- IVA --}}
                        <label class="mr-5" id="productIva">
                            IVA: 
                            <b></b>
                        </label>

                        {{-- Gestión de lotes --}}
                        <label class="mr-5" id="productBatches">
                            {!! ucfirst(trans('textos.lotes_gestion')) !!}:
                            <span></span>
                        </label>

                        {{-- Acciones --}}
                        {{-- Editar --}}
                        <a target="_blank" href="" class="btn btn-xs btn-info float-right ml-1" id="productEdit" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}" style="margin-top: 0;"><i class="fa fa-edit"></i></a>

                        {{-- Stock --}}
                        <a target="_blank" href="{{-- {{ route('stocks.show', $product) }} --}}" class="btn btn-xs btn-info float-right" id="productStockHistory" rel="tooltip" title="{{ ucfirst(trans('textos.movimientos_historico_ver')) }}" style="margin-top: 0;"><i class="fa fa-tasks"></i></a>
                    </div>
                    <hr>

                    {{-- Stock por almacenes --}}
                    <h3 class="mb-2 mt-4">{{ ucfirst(trans('textos.stock_almacenes')) }}</h3>

                    <div class="row mt-3">
	                    <div class="col-12">
		                    <table class="table" id="tblStoresStock">
		                        <thead>
		                            <tr>
		                                <th>{{ ucfirst(trans('textos.almacen')) }}</th>
		                                <th>{{ ucfirst(trans('textos.lote')) }}</th>
		                                <th class="text-center">Stock</th>
		                                <th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
		                            </tr>
		                        </thead>

		                        <tbody>
		                        	
		                        </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <b>{{ ucfirst(trans('textos.total')) }}</b>
                                        </td>
                                        <td class="total-stock text-right">
                                            <b></b>
                                        </td>
                                        <td>                                      
                                        </td>
                                    </tr>
                                </tfoot>
		                    </table>

                            <p class="text-center text-warning"><b>{{ trans('textos.almacen_selec_texto') }}</b></p>
	                	</div>
                	</div>

                    <div class="row" id="movementExplanation">
                        <div class="col-12">
                                
                        </div>     
                    </div>

                    <h3 id="storeName" class="mt-3"><u></u></h3>

                    <form method="post" autocomplete="off" action="{{ route('stocks.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}
                        <input type="hidden" name="stock_id" id="stockId">
                        <input type="hidden" name="product_id" id="stockProductId">
                        <input type="hidden" name="store_id" id="stockStoreId">
                        <input type="hidden" name="batch_id" id="stockBatchId">

                        <div class="form-group row mt-4">
                            {{-- Movimiento stock --}}
                            <label for="movement" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.movimiento')) !!}*</label>
                            <div class="col-lg-4">
                                <x-select name="movement" class="select2 desactivado" id="stockMov" label="movimiento_selec" :options="$movements_arr" required/>
                            </div>
                        
                            {{-- Cantidad --}}
                            <label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!}*</label>
                            <div class="col-lg-2">
                                <x-input type="text" name="qty" id="stockQty" class="text-right set-decimals desactivado" autocomplete="off" maxlength="10" required/>
                            </div>
                        </div>

                        <div class="form-group row" id="withBatch">
                            {{-- Lotes --}}
                            <label for="batch" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote')) !!}</label>
                            <div class="col-lg-4">
                                <x-select name="batch" class="select2 aboutBatch" id="batches" label="lote_selec" :options="[]"/>
                            </div>

                            {{-- Nuevo lote --}}
                            <label for="new_batch" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_nuevo')) !!}</label>
                            <div class="col-lg-4 mb-3">
                                <x-input type="text" class="aboutBatch" id="newBatch" name="new_batch" autocomplete="off" maxlength="25"/>
                            </div>

                            {{-- Patrones de lotes --}}
                            @if(count($batch_patterns))
                                <label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patrones')) !!}</label>
                                <div class="col-lg-4">
                                    <x-select name="pattern_id" class="select2 aboutBatch" id="batchPattern" label="lotes_patrones" :options="$batch_patterns"/>    
                                </div>
                            @endif

                            {{-- Fecha caducidad --}}
                            <label for="expiry_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_caducidad')) !!}</label>
                            <div class="col-lg-4">
                                <x-date-picker name="expiry_date" id="expiryDate" :format="$languages[config('app.locale')][4]" disabled/>
                            </div>
                        </div>

                        <div class="form-group row" id="withOrder_">
                            {{-- Pedidos --}}
                            <label for="order" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido')) !!}</label>
                            <div class="col-lg-4">
                                <x-select name="order" class="select2" id="productOrders" label="pedido_selec" :options="[]"/>    
                            </div>
                        </div>

                        <div class="form-group row" >
                            {{-- Almacén destino --}}
                            <label for="to_store" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.almacen_destino')) !!}</label>
                            <div class="col-lg-4">
                                @php
                                $stores_ = [];
                                foreach($stores as $st){
                                    $stores_[$st->id] = $st->name;
                                }
                                @endphp

                                <x-select name="to_store" class="select2 typeX" id="toStore" label="almacen_destino_selec" :options="$stores_" disabled/>

                                <x-input-errors name="store_id"/>
                            </div>

                            {{-- Empleados --}}
                            <label for="for_user" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empleados')) !!}</label>
                            <div class="col-lg-4">
                                <x-select name="for_user" class="select2 typeX" label="empleado_selec" :options="$employees" disabled/>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- Nº documento --}}
                            <label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
                            <div class="col-lg-4">
                                <x-input type="text" name="doc_num" autocomplete="off" maxlength="100"/>
                            </div>

                            {{-- Documento --}}
                            <label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
                            <div class="col-lg-4">
                                <x-input-file name="doc"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- Observaciones --}}
                            <label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                            <div class="col-lg-10">
                                <x-input-textarea name="observations"/>   
                            </div>
                        </div>

                        <x-btn-submit name="submit_save" class="desactivado" label="{{ trans('textos.guardar') }}"/>
                    </form>
                </div>

                {{-- Aviso antes de seleccionar producto --}}
                <div class="productFirstScreen" @if($errors->any()) style="display: none;" @endif>
                	<h2 class="text-center text-warning mb-3">
                        {{ trans('textos.producto_no_seleccionado') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
//Acciones al seleccionar producto:
function selectProduct(products, currency, product_id){
    $('#stockProductId').val(product_id);
    $('#stockMov, #stockQty, #batches, #newBatch', '#batchPattern').prop('disabled', true);
    $('#tblStoresStock tbody').empty();

    //Stock por almacenes:
    $.get('/admin/stocks/'+product_id+'/stores-stock', function (response, state){
        if(response){
            var total_stock = 0;
            response.forEach(function (item, index, arr){
                console.log(item);
                total_stock = parseFloat(total_stock) + parseFloat(item.stock);
                var batch_ = item.batch == 'lote_0'? '':item.batch;
                var batch_id_ = item.batch != ''? item.batch_id:'';     //Si el producto no tiene gestión de lotes, no se pasa el batch_id.
                var tr = '<tr>';
                    tr += '<td>'+item.store+'</td>';
                    tr += '<td>';
                        if(item.batch != 'lote_0' && batch_id_ != ''){
                            tr += '<a target="_blank" href="/admin/batches/'+batch_id_+'/edit">';
                        }
                            tr += batch_;
                        if(item.batch != 'lote_0' && batch_id_ != ''){
                            tr += '</a>';
                        }
                    tr += '</td>';
                    tr += '<td class="text-right">'+item.stock+'</td>';
                    tr += '<td class="text-right">';
                        tr += '<a href="#" class="btn btn-xs btn-info btnSelectBatch" data-stockid="'+item.stock_id+'" data-stock="'+item.stock+'" data-store="'+item.store+'" data-storeid="'+item.store_id+'" data-batchid="'+batch_id_+'" rel="tooltip" title="'+txtLoteSelec+'"><i class="fa fa-filter"></i></a>';
                    tr += '</td>';
                tr += '</tr>';
                $('#tblStoresStock tbody').append(tr);           
            });
            $('.total-stock b').text(total_stock.toFixed(2));
        }
    });

    $('.productFirstScreen').fadeOut();
    $('.productForm').fadeIn();

    for(const p of products){
        if(p.id == product_id){
            product = p;
            break;
        }
    }

    $('#productTitle u').text(product.name);
    const date = new Date(product.created_at);
    $('#productAlta b').text(date.toLocaleDateString());
    var ref = product.ref? product.ref:product.manual_ref;
    $('#productRef b').text(ref);
    $('#productPrice b').text(product.price+currency);
    $('#productIva b').text(product.iva+'%');
    var batch = product.batch == 1? '<i class="fa fa-check text-success ml-1"></i>':'<i class="fa fa-ban text-danger ml-1"></i>';
    $('#productBatches span').html(batch);
    $('#productEdit').attr('href', '/admin/products/'+product_id+'/edit'); 
    $('#productStockHistory').attr('href', '/admin/stocks/'+product_id+'/show'); 

    //Lotes:
    if(product.batch == 1){
        $('#withBatch').slideDown();
        //Lotes activos del producto:
        $.get('/admin/batches/'+product_id+'/product-batches', function (response, state){
            if(response){
                $('#batches').empty(); //.append('<option value="">Selecciona lote</option>');
                response.forEach(function (item, index, arr){
                    if(item.batch_num != '{{ config('constants.NOBATCH_') }}'){
                        var option = '<option value="'+item.id+'">'+item.batch_num+'</option>';
                        $('#batches').append(option);
                    }
                });
            }
        });

    }else{
        $('#withBatch').slideUp();
    }
}

$(document).ready(function () {
	var currency = '{!! $currency->symbol !!}';
	var products = {!! json_encode($products) !!};
    var movements = {!! json_encode($movements) !!};
    var stores = {!! json_encode($stores_) !!};
    var product_id = @if($product_id) {{ $product_id }} @else false @endif;
	var product = '';
    $('#withOrder_').hide();

    //Inputs inactivos por defecto:
    $('.desactivado, .aboutBatch').prop('disabled', true);

    //Producto recibido por parámetro:
    if(product_id){
        selectProduct(products, currency, product_id);    
    }

	// Selección producto:
	$('#product').on('change', function(e){ 
        e.preventDefault();
		product_id = $(this).val();  

		if(product_id != ''){
            selectProduct(products, currency, product_id);    
		}
    });

    //Selección lote de almacén:
    $(document).on('click', '.btnSelectBatch', function(e){
        e.preventDefault();
        var elem = $(this);
        var stock_id = elem.data('stockid');
        var stock = elem.data('stock');
        var store_id = elem.data('storeid');
        var store = elem.data('store');
        var batch_id = elem.data('batchid');

        //Activamos inputs disabled si no viene declarado el lote:
        $('.desactivado').prop('disabled', false);
        if(batch_id == ''){
            $('.aboutBatch').prop('disabled', false);
        }

        $('#tblStoresStock td').removeClass('bg-warning');
        elem.closest('tr').find('td').siblings().addClass('bg-warning');

        $('#stockId').val(stock_id);
        $('#stockStoreId').val(store_id);
        $('#storeName u').text(store);
        $('#stockBatchId').val(batch_id);

        //Reseteamos las opciones de almacén de destino sin el almacén de origen:
        if(store > 0){
            $('#toStore').empty();

            for(const k in stores){
                if(k != store){
                    var option = '<option value="'+k+'">'+stores[k]+'</option>';
                    $('#toStore').append(option);
                }
            }
        }

        //Seteando opciones de lote:
        // if(batch_id != ''){
        //     var arr_batches = [batch_id];
        //     $('#batches').val(arr_batches);
        //     $('#batches').trigger('change');
        // }
    });

    //Seleccionar tipo de movimiento:
    $('#stockMov').on('change', function(e){
        var mov = e.target.value;
        movements.forEach(function(item, index){
            if(item.id == mov){
                var html = '<div id="movementExplanation_" class="p-3"><b>'+item.name+':</b> '+item.explanation_+'</div>';
                $('#movementExplanation div').empty().html(html);
            
                if(item.sign == 'x'){
                    $('.typeX').removeAttr('disabled');
                }else{
                    $('.typeX').prop('disabled', true);    
                }

                //Pedidos de compra del producto:
                if(item.acronym == 'ep'){
                    $('#withOrder_').slideDown();
                    $.get('/admin/products/'+product_id+'/purchase-orders', function (response, state){
                        if(response){
                            $('#productOrders').empty();
                            $('#productOrders').append('<option value="">'+txtPedidoSelec+'</option>');
                            response.forEach(function (item, index, arr){
                                var order_num = item.order_num? item.order_num:(item.order_old_num? item.order_old_num:item.id);
                                var order = item.company+' - '+order_num;
                                var option_ = '<option value="'+item.id+'">'+order+'</option>';
                                $('#productOrders').append(option_);
                            });   

                            $('#productOrders').select2(); 
                        }
                    });

                //Pedidos de venta:
                }else if(item.acronym == 'sp'){
                    $('#productOrders').empty();
                    $('#withOrder_').slideDown();

                }else{
                    $('#withOrder_').slideUp();    
                }
            }
        });
    });

    //Toggle de lotes: si seleccionamos uno existente no puede haber lote nuevo y viceversa.
    $('#batches').on('change', function(){
        var b = $(this).val();
        if(b > 0){
            $('#newBatch').val('');
            // $('#batchPattern').val([]);
            // $('#batchPattern').trigger('change');
            $('#batchPattern').val(null).trigger('change.select2');
            $('#expiryDate').val('').prop('disabled', true);
        }
    });
    $('#newBatch').on('change', function(){
        var b_ = $(this).val();
        if(b_ != ''){
            // $('#batches').val([]);
            // $('#batches').trigger('change'); 
            $('#batches').val(null).trigger('change.select2');
            //Fecha caducidad nuevo lote:
            $('#expiryDate').prop('disabled', false);   
            // $('#batchPattern').val([]);
            // $('#batchPattern').trigger('change');
            $('#batchPattern').val(null).trigger('change.select2');
        }else{
            $('#expiryDate').val('').prop('disabled', true);    
        }
    });
    $('#batchPattern').on('change', function(){
        var b__ = $(this).val();
        if(b__ != ''){
            $('#batches').val([]);
            $('#batches').trigger('change'); 
            //Fecha caducidad nuevo lote:
            $('#expiryDate').prop('disabled', false);  
            $('#newBatch').val(''); 
        }else{
            $('#expiryDate').val('').prop('disabled', true);    
        }
    });
});
</script>
@endpush

@endsection