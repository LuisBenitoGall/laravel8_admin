@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myCompany" value="{{ $company->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
        {{-- Nuevo producto - condiciones 
			1. Disponer de permisos de creación de producto.
			...
        --}}
		@can('purchase-products.create')
    		<a href="{{ route('purchase-products.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row">
	{{-- Catálogo de empresa --}}
	@if($company)
		<div class="col-12 mb-3">
			<h3>
				{{ ucfirst(trans('textos.productos_contables')) }}
				<u>{{ $company->name }}</u>
			</h3>
			<p class="text-warning">{{ trans('textos.productos_contables_texto') }}</p>
		</div>	
	@endif

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblPurchaseProducts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.iva')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.cuenta_contable')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection