@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
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
            	<div class="productForm">
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
		                    </table>

                            <p class="text-center text-warning"><b>{{ trans('textos.almacen_selec_texto') }}</b></p>
	                	</div>
                	</div>

                    <form method="post" autocomplete="off" action="{{ route('stocks.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}
                        <input type="hidden" name="stock_id" id="stockId">
                        <input type="hidden" name="product_id" id="stockProductId">
                        <input type="hidden" name="store_id" id="stockStoreId">

                        <div class="form-group row mt-4">
                            {{-- Movimiento stock --}}
                            <label for="movement" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.movimiento')) !!}*</label>
                            <div class="col-lg-4">
                                <x-select name="movement" class="select2 desactivado" id="stockMov" label="movimiento_selec" :options="$movements_arr" required/>
                            </div>
                        
                            {{-- Cantidad --}}
                            <label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!}*</label>
                            <div class="col-lg-2">
                                <x-input type="text" name="qty" class="text-right set-decimals desactivado" autocomplete="off" maxlength="10" required/>
                            </div>
                        </div>

                        <div class="form-group row" id="withBatch">
                            {{-- Lotes --}}
                            <label for="batch" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote')) !!}</label>
                            <div class="col-lg-2">
                                <x-select name="batch" class="select2" label="lote_selec" :options="[]"/>
                            </div>

                            {{-- Nuevo lote --}}
                            <label for="new_batch" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_nuevo')) !!}</label>
                            <div class="col-lg-2">
                                <x-input type="text" name="new_batch" autocomplete="off" maxlength="25"/>
                            </div>

                            {{-- Fecha caducidad --}}
                            <label for="expiry_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_caducidad')) !!}</label>
                            <div class="col-lg-2">
                                <x-date-picker name="expiry_date" id="expiryDate" :format="$languages[config('app.locale')][4]"/>
                            </div>
                        </div>

                        <div class="form-group row" id="typeX">
                            {{-- Almacén destino --}}
                            <label for="to_store" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.almacen_destino')) !!}</label>
                            <div class="col-lg-4">
                                @php
                                $stores_ = [];
                                foreach($stores as $st){
                                    $stores_[$st->id] = $st->name;
                                }
                                @endphp

                                <x-select name="to_store" class="select2" label="almacen_destino_selec" :options="$stores_"/>
                            </div>

                            {{-- Empleados --}}
                            <label for="for_user" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.almacen_destino')) !!}</label>
                            <div class="col-lg-4">
                                <x-select name="for_user" class="select2" label="empleado_selec" :options="$employees"/>
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
                <div class="productFirstScreen">
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

    $('#tblStoresStock tbody').empty();

    //Stock por almacenes:
    $.get('/admin/stocks/'+product_id+'/stores-stock', function (response, state) {
        if(response){
            response.forEach(function (item, index, arr){
                var batch_ = item.batch == 'lote_0'? '':item.batch;
                var tr = '<tr>';
                    tr += '<td>'+item.store+'</td>';
                    tr += '<td>'+batch_+'</td>';
                    tr += '<td class="text-right">'+item.stock+'</td>';
                    tr += '<td class="text-right">';
                        tr += '<a href="#" class="btn btn-xs btn-info btnSelectBatch" data-stockid="'+item.stock_id+'" data-stock="'+item.stock+'" data-store="'+item.store_id+'" data-batch="'+item.batch_id+'" rel="tooltip" title="'+txtLoteSelec+'"><i class="fa fa-filter"></i></a>';
                    tr += '</td>';
                tr += '</tr>';
                $('#tblStoresStock tbody').append(tr);           
            });
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
    }else{
        $('#withBatch').slideUp();
    }
}

$(function(){
	var currency = '{!! $currency->symbol !!}';
	var products = {!! json_encode($products) !!};
    var movements = {!! json_encode($movements_arr) !!};
    var product_id = @if($product_id) {{ $product_id }} @else false @endif;
	var product = '';

    //Inputs inactivos por defecto:
    $('.desactivado').prop('disabled', true);

    //Producto recibido por parámetro:
    if(product_id){
        selectProduct(products, currency, product_id);    
    }

	// Selección producto:
	$('#product').on('change', function(e){ 
		product_id = $(this).val();  

		if(product_id != ''){
            selectProduct(products, currency, product_id);    
		}
    });

    //Selección lote de almacén:
    $(document).on('click', '.btnSelectBatch', function(){
        var elem = $(this);
        var stock_id = elem.data('stockid');
        var stock = elem.data('stock');
        var store = elem.data('store');
        var batch = elem.data('batch');

        //Activamos inputs disabled:
        $('.desactivado').prop('disabled', false);

        $('#tblStoresStock td').removeClass('bg-warning');
        elem.closest('tr').find('td').siblings().addClass('bg-warning');

        $('#stockId').val(stock_id);
        $('#stockStoreId').val(store);
    });

    //Seleccionar tipo de movimiento:
    $('#stockMov').on('change', function(e){
        var mov = e.target.value;
        for(const m of movements){
            console.log(p.name);

        }
    });
});
</script>
@endpush

@endsection