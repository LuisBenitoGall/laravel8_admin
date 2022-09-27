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
                        <a target="_blank" href="{{-- {{ route('products.edit', $product) }} --}}" class="btn btn-xs btn-info float-right ml-1" id="productEdit" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}"><i class="fa fa-edit"></i></a>

                        {{-- Stock --}}
                        <a target="_blank" href="{{-- {{ route('stocks.show', $product) }} --}}" class="btn btn-xs btn-info float-right" id="productStockHistory" rel="tooltip" title="{{ ucfirst(trans('textos.movimientos_historico_ver')) }}"><i class="fa fa-tasks"></i></a>
                    </div>
                    <hr>

                    <h3 class="mb-2 mt-4">{{ ucfirst(trans('textos.stock_almacenes')) }}</h3>

                </div>

                {{-- Aviso antes de selecciona producto --}}
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
$(function(){
	var currency = '{!! $currency->symbol !!}';
	var products = {!! json_encode($products) !!};
	//console.log(products);
	var product_id = '';
	var product = '';

	// Selección producto:
	$('#product').on('change', function(e){ 
		product_id = $(this).val();  

		if(product_id != ''){
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
                                
                           


		}

    });

});
</script>
@endpush

@endsection