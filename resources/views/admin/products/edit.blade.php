@extends('admin.partials.layout')

@section('content')

@if(isset($product))
<input type="hidden" id="companyCurrency" value="{{ $currency }}">
<input type="hidden" id="productId" value="{{ $product->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('products.index')
			<a href="{{ route('products.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_productos') !!}
			</a>
		@endcan

		@can('products.create')
    		<a href="{{ route('products.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
    		</a>
        @endcan

		{{-- Generar serials --}}
		{{-- <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-serials">
			<i class="fa fa-sort-numeric-down"></i>
			{!! ucfirst(trans('textos.serializacion')) !!}
		</a> --}}

		{{-- Ordenes de trabajo --}}
		@if($module_production)
			@can('work-orders.create')
				<a href="" class="btn btn-info">
	    			<i class="fa fa-industry"></i>
	    			{!! ucfirst(trans('textos.trabajo_orden_generar')) !!}
	    		</a>	
			@endcan
		@endif
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

			  		<li class="nav-item @if($tab == 'parameters') active @endif">
			    		<a class="nav-link @if($tab == 'parameters') active @endif" href="#tab-parameters" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.parametros')) !!}</a>
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
						{{-- @include('admin.products.partials.product-categories') --}}	
					</div>

					{{-- Parámetros --}}
					<div class="tab-pane fade @if($tab == 'parameters') show active @endif" role="tabpanel" id="tab-parameters">
						{{-- @include('admin.products.partials.product-parameters') --}}	
					</div>

					{{-- Producción --}}
					@if($module_production)
						<div class="tab-pane fade @if($tab == 'production') show active @endif" role="tabpanel" id="tab-production">
							{{-- @include('admin.products.partials.product-production') --}}	
						</div>
					@endif

					{{-- Histórico de precios --}}
					<div class="tab-pane fade @if($tab == 'prices') show active @endif" role="tabpanel" id="tab-prices">
						{{-- @include('admin.products.partials.product-prices') --}}	
					</div>

					{{-- Series --}}
					{{-- @if($serials->count() > 0) --}}
						<div class="tab-pane fade @if($tab == 'serials') show active @endif" role="tabpanel" id="tab-serials">
							{{-- @include('admin.products.partials.product-serials')	 --}}
						</div>
					{{-- @endif --}}
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Asignar formulario de presupuesto por cliente.
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
});
</script>
@endpush

@endif

@endsection