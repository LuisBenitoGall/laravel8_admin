@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtYear" value="{{ $year }}">
<input type="hidden" id="dtCurrency" value="{{ $currency->symbol }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.index')
			<a href="{{ route('orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_pedidos') !!}
			</a>
		@endcan
	</div>
</div>

{{-- Filtro --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				@php
				$ruta = $order_type == 'compras'? 'control-panel-purchases-year':'control-panel-sales-year';
				@endphp

				<form method="post" autocomplete="off" action="{{ route('orders.'.$ruta) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="order_type" id="orderType" value="{{ $order_type }}">

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])

						{{-- Centros de trabajo --}}
						{{-- @if(count($workplaces) > 1)
							<label for="workplace" class="col-2 col-form-label">{{ ucfirst(trans('textos.centro_trabajo')) }}</label>
	                    	<div class="col-3">
	                    		<x-select name="workplace" class="select2" label="centro_trabajo_selec" :options="$workplaces"/>
	                    	</div>
	                    @elseif(count($workplaces) == 1)
	                    	@foreach($workplaces as $i => $wk)
	                    		<input type="hidden" name="workplace" value="{{ $i }}">
	                    	@endforeach
	                    @endif --}}

						<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}" inline/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		@php
		$tableId = $order_type == 'compras'? 'tblOrdersControlPanelPurchases':'tblOrdersControlPanelSales';
		@endphp

		<table class="table table-hover dtabla" id="{{ $tableId }}">
			<thead>
				<tr>
					<th class="wdt150">{!! ucfirst(trans('textos.facturacion_prevista')) !!}</th>
					
					@php
					$lbl1 = $order_type == 'compras'? 'proveedor':'cliente';
					@endphp
					<th class="wdt200">{!! ucfirst(trans('textos.'.$lbl1)) !!}</th>
					{{-- <th class="wdt150">{!! ucfirst(trans('textos.area_negocio')) !!}</th> --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.pedido_num_abrv')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
					<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.responsable')) !!}</th>
					@if($order_type != 'compras')
						<th class="wdt150">{!! ucfirst(trans('textos.centro_trabajo')) !!}</th>
						<th class="text-center wdt150">{!! ucfirst(trans('textos.cliente_final')) !!}</th>
					@endif
					<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.a_facturar')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.facturado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td></td>
					<td></td>
					{{-- <td></td> --}}
					@if($order_type != 'compras')
						<td></td>
						<td></td>
					@endif
					<td></td>
					<td></td>
					<td></td>
					{{-- <td></td> --}}
					<td class="text-right"><b>{{ trans('textos.total') }}</b></td>
					<td class="text-right"></td>
					{{-- <td></td> --}}
					<td class="text-right"><b>{{ trans('textos.a_facturar') }}</b></td>
					<td class="text-right"></td>
					{{-- <td></td> --}}
					<td></td>
				</tr>	
			</tfoot>
		</table>
	</div>
</div>

@endsection