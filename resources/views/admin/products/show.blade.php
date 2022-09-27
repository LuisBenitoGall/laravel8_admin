@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@if($product->company_id == session('currentCompany'))
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

	        @can('products.edit')
	    		<a target="_blank" href="{{ route('products.edit', $product) }}" class="btn btn-info">
	    			<i class="fa fa-edit"></i>
	    			{!! ucfirst(trans('textos.producto_editar')) !!}
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

				<div class="row">
					{{-- Carousel --}}
                    <div class="col-md-5 mb-5">
                    	<div class="product-images">
                    		@foreach($docs as $doc)
                    			<div class="">
	                        		<img src="{{ asset('img/products/'.$doc->doc) }}" class="img-fluid" alt="{{ $product->name }}">
		                        </div>
                    		@endforeach
                    	</div>
                    </div>

                    {{-- Info --}}
                    <div class="col-md-7">
                    	<h2 class="font-bold m-b-xs">{{ ucfirst($product->name) }}</h2>

                    	{{-- Descripción --}}
                        @php
                        $descriptions = $product->description? unserialize($product->description):false;
                        $product_description = $descriptions && $descriptions[session()->get('locale')]? $descriptions[session()->get('locale')]:false;
                        @endphp
                        @if($product_description) 
                            <small>
                            	{!! $product_description !!}
                        	</small>
                        @endif

                        {{-- Precio --}}
                        <div class="m-t-md">
                        	@php
                        	//Verificamos que el precio para cliente venga customizado:
                        	$price = $customer_product && $customer_product->price? $customer_product->price:$product->price;
                        	@endphp

                            <h2 class="product-main-price">{{ $price }}{{ $currency->symbol }} <small class="text-muted"></small> </h2>
                        </div>
                        <hr>

                        {{-- Descripción ampliada --}}
                        <h4>{{ ucfirst(trans('textos.descripcion')) }}</h4>
                        <div class="small text-muted">
                        	@php
	                        $long_descriptions = $product->long_description? unserialize($product->long_description):false;
	                        $product_long_description = $long_descriptions && $long_descriptions[session()->get('locale')]? $long_descriptions[session()->get('locale')]:'';
	                        @endphp	

	                        {!! $product_long_description !!}	
                        </div>
                        <hr>

                        {{-- CTA's --}}
                        <div>
                            <div class="btn-group">
                            	{{-- Favoritos --}}
                                <button class="btn btn-primary btn-sm btnAddFavorite" data-product="{{ $product->id }}">
                                	@if($favorite)
                                		<i class="fa fa-star destacado"></i> {{ trans('textos.favorito') }}	
                                	@else
                                		<i class="fa fa-star"></i> {{ trans('textos.favoritos_add') }} 
                                	@endif
                                </button>
                                <button class="btn btn-white btn-sm"><i class="fa fa-star"></i> Add to wishlist </button>
                                <button class="btn btn-white btn-sm"><i class="fa fa-envelope"></i> Contact with author </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr>

                <div class="row">
                	{{-- Unidades de venta --}}
                	<div class="col-md-4 col-lg-3">
                		<h4>{{ trans('textos.unidades_venta') }}</h4>

                		@if($units->count())
                			<ul>
                				@foreach($units as $unit)
                					<li>· {{ $unit->name }} ({{ $unit->symbol }})</li>
                				@endforeach
                			</ul>
                		@else
                			<p class="text-warning mt-4">
                				{{ trans('textos.unidades_venta_no_definidas') }}
                			</p>
                		@endif
                	</div>

                	{{-- Tags --}}
                	<div class="col-md-4 col-lg-3">
                		<h4>Tags</h4>
                	</div>
                </div>
			</div>
		</div>
	</div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/slick/slick.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/slick/slick-theme.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/plugins/slick/slick.min.js') }}"></script>
<script>
$(document).ready(function(){
	// Carousel:
    $('.product-images').slick({
        dots: true
    });
});
</script>
@endpush

@endsection	