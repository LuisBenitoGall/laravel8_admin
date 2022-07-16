<div class="ibox">
	<div class="ibox-content product-box active">	
		<div class="product-imitation square-div">
			@if($product->Image)
				<img src="{{ asset('img/products/'.$product->Image) }}" alt="{{ $product->name }}">
			@else
            	<i class="fa fa-gift"></i>
            @endif
        </div>
        <div class="product-desc">
        	{{-- Precio --}}
            @php
            $price = $product->priceForCustomer? $product->priceForCustomer:$product->price;
            @endphp
            <span class="product-price">{{ $price }}{{ $currency }}</span>

            {{-- Nombre producto --}}
            @php
            $translations = $product->translations? unserialize($product->translations):false;
            $product_name = $translations && $translations[session()->get('locale')]? $translations[session()->get('locale')]:$product->name;
            @endphp
            <label class="product-name">{{ ucfirst($product_name) }}</label>

            {{-- Descripción --}}
            @php
            $descriptions = $product->description? unserialize($product->description):false;
            $product_description = $descriptions && $descriptions[session()->get('locale')]? $descriptions[session()->get('locale')]:false;
            @endphp
            @if($product_description) 
                <div class="small m-t-xs">
                	{!! App\Helpers\helpers::truncate($product_description, 100) !!}
            	</div>
            @endif

            {{-- Link producto --}}
        	<div class="m-t text-right">
        		{{-- Añadir al pedido --}}
        		@if($order_)
	        		<a href="#" class="btn btn-xs btn-info btnAddProduct" title="{{ trans('textos.pedido_agregar') }}" data-product="{{ $product->id }}">
	        			<i class="fa fa-cart-plus"></i>
	        		</a>
	        	@endif

        		{{-- Ficha producto --}}
            	<a target="_blank" href="{{ route('products.show', $product) }}" class="btn btn-xs btn-outline btn-primary">Info <i class="fa fa-long-arrow-alt-right"></i></a>
        	</div>
        </div>
    </div>
</div>