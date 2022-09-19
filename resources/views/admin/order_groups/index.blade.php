@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.create')
			<a href="{{ route('order-groups.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.agrupacion_nueva')) !!}
    		</a>
    	@endcan
    </div>
</div>

<input type="hidden" id="dtController" value="orders">

{{-- Formulario filtro --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('order-groups.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
				
					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.selec_cliente_grupo_pedidos') !!}</p>
						</div>

						{{-- Selector de cliente --}}
						@include('admin.partials.selector-customer', ['all' => false, 'selected' => '', 'required' => true])

						{{-- Agrupación --}}
						<label for="og_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agrupacion_num')) !!} </label>
						<div class="col-lg-2">
							<x-input type="text" name="og_id" autocomplete="off" maxlength="25"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@if($customer)
	<input type="hidden" id="myCustomer" value="{{ $customer->id }}">
	<input type="hidden" id="myYear" value="{{ $year }}">	

	<div class="row">
		<div class="col-12 mb-3">
			<h3>
				{!! trans('textos.agrupacion_pedidos') !!} 
				<b>{{ $customer->name }}</b> 
			</h3>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblOrderGroups">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th>
						<th class="wdt200">{!! ucfirst(trans('textos.agrupacion')) !!}</th>
						<th class="wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th> 
						<th class="text-center wdt50">{!! ucfirst(trans('textos.bolsa')) !!}</th> 
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt150">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>

@else
	<div class="row">
		<div class="col-12 text-center pt-4">
			<h2>{!! trans('textos.filtra_agrupaciones') !!}</h2>
		</div>
	</div>	
@endif

@endsection