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

<div class="row mb-3">
	{{-- Column left --}}
	<div class="col-md-4">
		<div class="card mb-3">
			<div class="card-body">
				<div class="form-group row">
					{{-- Productos --}}
					<label for="type" class="col-12">{!! ucfirst(trans('textos.productos')) !!}*</label>
					<div class="col-12">
						@php
						$products_ = [];
						foreach($products as $product){
							$products_[$product->id] = $product->name;
						}
						@endphp

						<x-select name="type" class="select2" label="producto_selec" :options="$products_" required/>
					</div>
				</div>

				{{-- Histórico productos seleccionados --}}
			</div>
		</div>
	</div>

	{{-- Column right --}}
	<div class="col-md-8">
		<div class="card mb-3">
			<div class="card-body">

			</div>
		</div>
	</div>
</div>

@endsection