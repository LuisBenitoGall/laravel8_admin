@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="orders">

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

		{{-- Informes - condiciones: 
			1. Exclusivo proveedor.
			2. Viene declarado el cliente.
		--}}
		@if($customer_id && $provider->id == session('currentCompany'))
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-set-report">
				<i class="fa fa-chart-bar"></i>
				{!! ucfirst(trans('textos.informes')) !!}
			</a>
		@endif
	</div>
</div>

{{-- Formulario filtro --}}
@include('admin.orders.partials.form-filter')

@if($customer_id || $provider_id)
	<input type="hidden" id="myCustomer" value="{{ $customer_id }}">
	<input type="hidden" id="myProvider" value="{{ $provider_id }}">
	<input type="hidden" id="myOwner" value="@if($owner){{ $owner }}@else{{ 0 }}@endif">
	<input type="hidden" id="myYear" value="{{ $year }}">
	<input type="hidden" id="myStatus" value="@if($status){{ $status }}@else{{ 0 }}@endif">
	<input type="hidden" id="myModuleAccounting" value="{{ $module_accounting }}">
	<input type="hidden" id="myOrderBuy" value="{{ $order_buy }}">

	{{-- Tabla --}}
	<div class="row">
		<div class="col-12 mb-3">
			<h3>
				@if($customer_id == 'all')
					{!! trans('textos.ventas_relacion_general') !!}

				@elseif($provider_id == 'all')
					{!! trans('textos.compras_relacion_general') !!}

				@else
					@if($customer->id == session('currentCompany'))
						{!! ucfirst(trans('textos.compras_a')) !!} <b>{{ $provider->name }}</b>
					@elseif($provider->id == session('currentCompany'))
						{!! ucfirst(trans('textos.ventas_a')) !!} <b>{{ $customer->name }}</b>
					@endif
				@endif
			</h3>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblOrders">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						{{-- <th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th> --}}
						<th class="wdt100">{!! ucfirst(trans('textos.responsable')) !!}</th>
						<th class="wdt150">
							@if($ordersType == 'compras')
								{!! ucfirst(trans('textos.proveedor')) !!}
							@else
								{!! ucfirst(trans('textos.cliente')) !!}
							@endif
						</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.agrupacion')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
						<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
						<!-- <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th> -->
						<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
						{{-- <th>{!! trans('textos.area_negocio') !!}</th> --}}
						{{-- <th class="wdt100">{!! trans('textos.area') !!}</th> --}}
						<th class="wdt100">{!! trans('textos.centro_coste') !!}</th>
						<th class="wdt50">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
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

{{-- Set report --}}
@include('admin.orders.partials.modal-set-report')

@push('scripts')
    <script>
        $(function(){
            //Toggle selector clientes o proveedores:
            $('#customerId').on('change', function(e){
                $('#providerId').val(['']).trigger('change.select2');
            });
            $('#providerId').on('change', function(e){
                $('select#customerId').val(['']).trigger('change.select2');
            });
        });
    </script>
@endpush

@endsection
