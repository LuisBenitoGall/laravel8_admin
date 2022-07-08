@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'products'])

        {{-- Nuevo producto - condiciones 
			1. Disponer de permisos de creación de producto.
			2. Que sea catálogo propio, no de proveedor.
			...
        --}}
		@can('products.create')
			@if(!$provider)
	    		<a href="{{ route('products.create') }}" class="btn btn-info">
	    			<i class="fa fa-plus"></i>
	    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
	    		</a>
	    	@endif
        @endcan
	</div>
</div>

<input type="hidden" id="myCompany" value="@if($provider) {{ $provider->id }} @endif">

{{-- Vista listado --}}
<div class="row">
	{{-- Catálogo de proveedor --}}
	@if($provider)
		<div class="col-12 mb-3">
			<h3>
				{{ trans('textos.proveedor_catalogo') }}
				<u>{{ $provider->name }}</u>
			</h3>
		</div>	
	@endif

	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblProducts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th>{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.producto_venta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
<div class="toggle-view-grid">
	{{-- Componente Products Grid --}}
	@livewire('products-grid', ['currency' => $currency, 'order_' => false, 'company' => $provider])

	@include('admin.partials.back-to-top')
</div>

{{-- Modals: --}}
{{-- Translations: --}}
@include('admin.languages.partials.modal-translations')

@endsection