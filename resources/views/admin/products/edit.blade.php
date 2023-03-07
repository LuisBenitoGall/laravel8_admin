@extends('admin.partials.layout')

@section('content')

@if(isset($product))
<input type="hidden" id="companyCurrency" value="{{ $currency }}">
<input type="hidden" id="productId" value="{{ $product->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de productos --}}
		@can('products.index')
			<a href="{{ route('products.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_productos') !!}
			</a>
		@endcan

		{{-- Formulario nuevo producto --}}
		@can('products.create')
    		<a href="{{ route('products.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
    		</a>
        @endcan

        {{-- Proveedores del producto - condiciones: 
        	1. Disponer de permisos de edición de proveedores.
			2. El producto debe tener gestión de stock.
			...
        --}}
        @can('providers.edit')
        	@if($product->stock_management)
        		<a href="{{ route('product-providers.index', $product) }}" class="btn btn-info">
	    			<i class="fa fa-building"></i>
	    			{!! ucfirst(trans('textos.proveedores')) !!}
	    		</a>	
        	@endif
        @endcan

		{{-- Generar serials --}}
		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-serials">
			<i class="fa fa-sort-numeric-down"></i>
			{!! ucfirst(trans('textos.serializacion')) !!}
		</a>

		{{-- Ordenes de trabajo - condiciones: 
			1. Permisos de creación de órdenes de trabajo.
			2. Módulo producción activo.
			3. Debe tratarse de un producto, no de un servicio.
			...
		--}}
		@can('work-orders.create')
			@if($module_production && $product->type == 'p')
				<a href="" class="btn btn-info">
	    			<i class="fa fa-industry"></i>
	    			{!! ucfirst(trans('textos.trabajo_orden_generar')) !!}
	    		</a>	
			@endif
		@endcan

		{{-- Eliminar producto - condiciones 
			1. Disponer de permisos para eliminar productos.
			...
		--}}
        @can('products.destroy')
        	<form method="POST" action="{{ route('products.destroy', $product) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.producto_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.producto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
            </form>
        @endcan
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.producto')) !!} 
					<u>{{ $product->name }}</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-3">
					{{-- Tipo --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.tipo')) !!}: 
						<b>
							@if($product->type == 'p')
								{!! ucfirst(trans('textos.producto')) !!}	
							@elseif($product->type == 's')
								{!! ucfirst(trans('textos.servicio')) !!}
							@endif
						</b>	
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $product->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $product->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-producto" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.producto')) !!}</a>
			  		</li>

					<li class="nav-item @if($tab == 'docs') active @endif">
			    		<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.imagenes')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'units') active @endif">
			    		<a class="nav-link @if($tab == 'units') active @endif" href="#tab-units" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.unidades_venta_medicion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'categories') active @endif">
			    		<a class="nav-link @if($tab == 'categories') active @endif" href="#tab-categories" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.categorias')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'attributes') active @endif">
			    		<a class="nav-link @if($tab == 'attributes') active @endif" href="#tab-attributes" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.atributos')) !!}</a>
			  		</li>

			  		@if($module_production)
				  		<li class="nav-item @if($tab == 'production') active @endif">
				    		<a class="nav-link @if($tab == 'production') active @endif" href="#tab-production" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.produccion')) !!}</a>
				  		</li>
				  	@endif

			  		<li class="nav-item @if($tab == 'prices') active @endif">
			    		<a class="nav-link @if($tab == 'prices') active @endif" href="#tab-prices" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.historico_precios')) !!}</a>
			  		</li>

					{{-- @if($serials->count() > 0) --}}
						<li class="nav-item @if($tab == 'serials') active @endif">
							<a class="nav-link @if($tab == 'serials') active @endif" href="#tab-serials" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.serializacion')) !!}</a>
						</li>
					{{-- @endif --}}
				</ul>

				<div class="tab-content pt-5">
					{{-- Datos producto: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-producto">
						@include('admin.products.partials.product-info')	
					</div>

					{{-- Imágenes: --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-docs">
						@include('admin.products.partials.product-docs')	
					</div>

					{{-- Unidades de venta --}}
					<div class="tab-pane fade @if($tab == 'units') show active @endif" role="tabpanel" id="tab-units">
						@include('admin.products.partials.product-units')	
					</div>

					{{-- Categorías --}}
					<div class="tab-pane fade @if($tab == 'categories') show active @endif" role="tabpanel" id="tab-categories">
						@include('admin.products.partials.product-categories')	
					</div>

					{{-- Atributos --}}
					<div class="tab-pane fade @if($tab == 'attributes') show active @endif" role="tabpanel" id="tab-attributes">
						@include('admin.products.partials.product-attributes')	
					</div>

					{{-- Producción --}}
					@if($module_production)
						<div class="tab-pane fade @if($tab == 'production') show active @endif" role="tabpanel" id="tab-production">
							{{-- @include('admin.products.partials.product-production') --}}	
						</div>
					@endif

					{{-- Histórico de precios --}}
					<div class="tab-pane fade @if($tab == 'prices') show active @endif" role="tabpanel" id="tab-prices">
						@include('admin.products.partials.product-prices')	
					</div>

					{{-- Números de serie --}}
					<div class="tab-pane fade @if($tab == 'serials') show active @endif" role="tabpanel" id="tab-serials">
						@include('admin.products.partials.product-serials')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Edit attribute --}}
@include('admin.product_attributes.partials.modal-edit-attribute', ['action' => 'product-attributes.update', 'serial_id' => false])

{{-- Add Serials: --}}
@include('admin.product_serials.partials.modal-add-serials')

@push('scripts')
<script>
$(function(){
	var data_types = {!! json_encode($data_types) !!};

	//Asignar unidad de producto.
    $(document).on('change', '.btnSetProductUnit', function(){
        var elem = $(this);
        var product_id = elem.find('.chk-product-unit').data('productid');
        var unit_id = elem.find('.chk-product-unit').data('unitid');
        $.get("/admin/product-units/status/"+product_id+"/"+unit_id+"", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        });     
    });

    //Toggle precio por porcentaje.
    $('#price').on('change', function(){
    	var price = $(this).val();
    	if(price != ''){
    		$('#priceByPercent').prop('disabled', true).val('');
    	}else{
    		$('#priceByPercent').prop('disabled', false);	
    	}
    });

    //Vincular / desvincular categorías a producto.
    $(document).on('change', '.btnProductCategory', function(){
    	var elem = $(this);
        var product_id = $('#productId').val();
        var category_id = elem.find('.chk').data('id');
        $.get("/admin/product-categories/store/"+product_id+"/"+category_id+"", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        });  
    });

    //Modal editar atributo de producto.
    $(document).on('click', '.btnEditAttribute', function(){
    	var id = $(this).data('id');
    	var value = $(this).data('value');
    	var values = $(this).data('values');
    	var datatype = $(this).data('type');
    	$('.dataType').hide();

    	$('#paId').val(id);

    	//Mostramos el input indicado según el tipo de dato.
    	//Boolean:
    	if(datatype == 10){
    		$('#type-boolean').fadeIn();
    		var rdb = value == 1? '#rdbTrue':'#rdbFalse';
    		$(rdb).prop('checked', true);

    	//Texto:
    	}else if(datatype == 20 || datatype == 30){
    		$('#type-textarea').fadeIn();
    		$('#paValueTextarea').trumbowyg('html', value);

    	//Fecha:
    	}else if(datatype == 40){
    		$('#type-datepicker').fadeIn();
    		$('#paValueDatepicker').val(value);

    	//Opciones:
    	}else if(datatype == 50){
    		$('#type-select').fadeIn();
    		var arr_options = values.split(",");
    		var arr_values = value.split(",");
    		//console.log(arr_values, arr_options);
    		$("#paValueSelect").select2({width: '100%', data: arr_options});
    		$("#paValueSelect").val(arr_values).trigger('change');

    	//Color:
    	}else if(datatype == 60){
    		$('#type-color').fadeIn();
    		var arr_options = values.split(' ');
    		var arr_values = value.split(",");
    		var html = '';

    		for(var i in arr_options){
    			//console.log(i);
    			if(arr_options[i] != ''){
    				var p = arr_options[i].split("-");
    				var checked = arr_values.includes(p[0])? 'checked':'';
    				html += '<div class="col-lg-4 mb-3">';
    					html += '<label class="custom-chk-rdb" rel="tooltip"><input type="checkbox" name="value_color[]" value="'+p[0]+'" '+checked+'><span class="chk chksmall" style="top: 3px;"></span></label>';
    					html += '<span class="colorEx mr-1" style="background-color: '+p[0]+';"></span> '+p[1];
    				html += '</div>';
    			}
    		}
    		
    		$('#paValueColor').html(html);

    		//console.log(arr_options, arr_values);
    	}


    	//$('#typeOptions').val(values);

    	// $('#type-'+show_type).fadeIn();


    	// //Seteando el valor según el input:
    	// if(show_type == 'textarea'){
    	// 	$('#paValueTextarea').trumbowyg('html', value);
    	// }else if(show_type == 'datepicker'){
    	// 	$('#paValueDatepicker').val(value);
    	// }else if(show_type == 'select'){
    	// 	//Convertimos string en array:
    	// 	var arr = values.split(",");
    	// 	$("#paValueSelect").select2({width: '100%', data: arr}).val(value);
    	// }
    });
});
</script>
@endpush

@endif

@endsection