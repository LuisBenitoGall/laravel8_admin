@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Panel ventas: --}}
		<a href="{{ route('orders.control-panel-sales') }}" class="btn btn-info">
			<i class="fa fa-chalkboard"></i>
			{!! ucfirst(trans('textos.panel_control_ventas')) !!}
		</a>

		{{-- Panel compras: --}}
		<a href="{{ route('orders.control-panel-purchases') }}" class="btn btn-info">
			<i class="fa fa-chalkboard"></i>
			{!! ucfirst(trans('textos.panel_control_compras')) !!}
		</a>

		{{-- Nueva venta --}}
		@if(session('companySettings') && session('companySettings')['customers_management'])
			@can('orders.create')
				<a href="{{ route('orders.create', 'sale') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.venta_nueva')) !!}
				</a>
			@endcan
		@endif

		{{-- Nueva compra --}}
		@if(session('companySettings') && session('companySettings')['providers_management'])
			@can('orders.create')
				<a href="{{ route('orders.create', 'purchase') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.compra_nueva')) !!}
				</a>
			@endcan
		@endif

		{{-- Informes: --}}
		{{-- @if($customerInfo && $providerInfo)
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-set-report">
				<i class="fa fa-chart-bar"></i>
				{!! ucfirst(trans('textos.informes')) !!}
			</a>
		@endif --}}
	</div>
</div>

<input type="hidden" id="dtController" value="orders">

{{-- Formulario filtro --}}
@include('admin.orders.partials.form-filter')

@if($customer_id || $provider_id)
	<input type="hidden" id="myCustomer" value="@if($customer){{ $customer->id }}@endif">
	<input type="hidden" id="myProvider" value="@if($provider){{ $provider->id }}@endif">
	<input type="hidden" id="myOwner" value="@if($owner){{ $owner }}@else{{ 0 }}@endif">
	<input type="hidden" id="myStatus" value="@if($status){{ $status }}@else{{ 0 }}@endif">
	<input type="hidden" id="myModuleAccounting" value="{{ $module_accounting }}">

	<div class="row">
		<div class="col-12 mb-3">
			<h3>
				@if($customer_id == 'all')
					{!! trans('textos.ventas_relacion_general') !!}

				@elseif($provider_id == 'all')
					{!! trans('textos.compras_relacion_general') !!}

				@else
					@if($customer)
						@if($customer->id == session('currentCompany'))
							{!! trans('textos.compras_de') !!} 
						@else
							{!! trans('textos.ventas_de') !!}
						@endif
						<b>{{ $provider->name }}</b>
					@endif

					@if($provider)
						{!! trans('textos.a') !!} 
						<b>{{ $customer->name }}</b> 
					@endif	
				@endif
			</h3>
		</div>
			
		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblOrders">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						{{-- <th>{!! ucfirst(trans('textos.pedido_num')) !!}</th> --}}
						<th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
						<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
						<!-- <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th> -->
						<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
						{{-- <th>{!! trans('textos.area_negocio') !!}</th> --}}
						{{-- <th class="wdt100">{!! trans('textos.area') !!}</th> --}}
						<th class="wdt100">{!! trans('textos.centro_coste') !!}</th>
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>

	{{-- Modals --}}
	{{-- Informes: --}}
	<div class="modal fade modal-set-report" id="modal-set-report" tabindex="-1" role="dialog" aria-labelledby="setReport" aria-hidden="true">
	    <div class="modal-dialog modal-lg" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="setReport">{!! ucfirst(trans('textos.informes')) !!}</h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	                <form method="post" autocomplete="off" action="{{ route('orders.export-by-range-dates') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="customer_id" value="@if($customer) {{ $customer->id }} @endif">
	                    <input type="hidden" name="provider_id" value="@if($provider) {{ $provider->id }} @endif">

	                    <div class="row">
	                    	<p class="col-12 text-warning">{!! trans('textos.informe_pedidos_rango_fechas') !!}</p>
	                    </div>

	                    {{-- Desde --}}
	                    <div class="form-group row">
							<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!} *</label>
							<div class="col-lg-4">
								<span class="deleteValue">
									<i class="fa fa-times"></i>
								</span>	

								<input type="text" name="from" class="form-control dateTimePicker" autocomplete="off" maxlength="15" data-fecha="" value="{{ old('from') }}" readonly required>

								@if($errors->has('from'))
	                                <span class="invalid-feedback" role="alert">
	                                  <strong>{{ $errors->first('from') }}</strong>
	                                </span>
	                            @endif
							</div>

							<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!} *</label>
							<div class="col-lg-4">
								<span class="deleteValue">
									<i class="fa fa-times"></i>
								</span>	

								<input type="text" name="to" class="form-control dateTimePicker endDay" autocomplete="off" maxlength="15" data-fecha="" value="{{ old('to') }}" readonly required>

								@if($errors->has('to'))
	                                <span class="invalid-feedback" role="alert">
	                                  <strong>{{ $errors->first('to') }}</strong>
	                                </span>
	                            @endif
							</div>
						</div>

						<x-btn-submit name="submit_set_report" label="{{ trans('textos.buscar') }}"/>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@else
	<div class="row">
		<div class="col-12 text-center pt-4">
			<h2>{!! trans('textos.filtra_pedidos') !!}.</h2>
		</div>
	</div>	
@endif

{{-- Modals: --}}
{{-- Factura --}}
{{-- @include('admin.orders.partials.modal-generate-invoice') --}}

@endsection