@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="productId" value="{{ $product->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
   		{{-- Editar producto --}}
   		@can('products.edit')
    		<a target="_blank" href="{{ route('products.edit', $product) }}" class="btn btn-info">
    			<i class="fa fa-edit"></i>
    			{!! ucfirst(trans('textos.producto_editar')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>
			{!! ucfirst(trans('textos.historico_movimientos')) !!} 
			<u>{{ $product->name }}</u> 
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductStockMovements">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th>{!! ucfirst(trans('textos.movimiento')) !!}</th>
					<th>{!! ucfirst(trans('textos.almacen')) !!}</th>
					<th>{!! ucfirst(trans('textos.lote')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.cantidad')) !!}</th>
					<th class="text-center">Stock</th>
					<th class="text-center">{!! ucfirst(trans('textos.documento')) !!}</th>
					<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection