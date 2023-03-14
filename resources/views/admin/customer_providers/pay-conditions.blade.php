@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route($environment.'.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			@if($environment == 'customers')
				{!! trans('textos.volver_clientes') !!}
			@else
				{!! trans('textos.volver_proveedores') !!}
			@endif
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-4">@if($environment == 'customers') {{ trans('textos.condiciones_cobro') }}  @else {{ trans('textos.condiciones_pago_de') }} @endif <u>{{ $company->name }}</u></h3>	

				@include('admin.customer_providers.partials.form-pay-conditions', ['return' => 'conditions'])
			</div>
		</div>
	</div>
</div>

{{-- Facturación por Áreas de negocio --}}
@if($areas->count() > 0)
	<div class="row">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-body">	
					<h3>{{ trans('textos.facturas_area_negocio') }} <u>{{ $company->name }}</u></h3>	
					<p class="text-warning">{{ trans('textos.facturas_area_negocio_aviso') }}</p>
					<br>

				</div>
			</div>
		</div>
	</div>
@endif

@endsection