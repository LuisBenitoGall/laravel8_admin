@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myOrder" value="{{ $order->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de presupuestos --}}
		@can('budgets.index')
			<a href="{{ route('budgets-from-providers.index') }}" class="btn btn-info" id="teeest">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_presupuestos') !!}
			</a>
		@endcan

		@if($order->customer_id == session('currentCompany'))
			@if(!$order->accepted_date && $order->customer_id == session('currentCompany'))
				<a href="{{ route('budgets.accept', $order->id) }}" class="btn btn-warning btn-confirm" data-msg="{!! ucfirst(trans('textos.aceptar_presupuesto_confirm')) !!}">
					<i class="fa fa-check"></i>
					{!! trans('textos.aceptar_presupuesto') !!}
				</a>
			@else
				<a href="{{ route('budgets.cancel', $order->id) }}" class="btn btn-danger btn-confirm" data-msg="{!! ucfirst(trans('textos.rechazar_presupuesto_confirm')) !!}">
					<i class="fa fa-ban"></i>
					{!! trans('textos.rechazar_presupuesto') !!}
				</a>	
			@endif
		@endif
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe presupuesto flotante: --}}
				@php
				//$order_amount_without_discount = $order->discount > 0? number_format($order_amount_without_discount, 2):false;
				@endphp

				{{-- @livewire('budget-amount', ['budget_amount' => $order_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => $order_amount_without_discount, 'environment' => 'budgets']) --}}

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.presupuesto')) !!} 
					<u>
						@if($order->budget_num)
							{{ $order->budget_num }}
						@else
							{{ $order->id }}
						@endif

						@if($order->name)
							- {{ $order->name }}
						@endif
					</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-3">
					{{-- Cliente --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.cliente')) !!}: 
						<b>
							{{ ucfirst($customer->name) }}
						</b>
					</label>

					{{-- Proveedor --}}
					@include('admin.orders.partials.data-provider')

					{{-- Cliente Final --}}
					@if($final_customer)
						<label class="mr-5">
						{!! ucfirst(trans('textos.cliente_final')) !!}: 
						<b>{{ $final_customer->name }}</b>
					</label>
					@endif

					{{-- Importe --}}
					<label class="mr-5">
						@livewire('budget-amount-mini', ['budget_amount' => $order_amount, 'currency' => $currency->symbol])
					</label>

					{{-- Descuento aplicado --}}
					@if($order->discount && $order->discount > 0)
						<label class="mr-5">
							{{ ucfirst(trans('textos.descuento_aplicado')) }}:
							<b>{{ $order->discount }}%</b>
						</label>
					@endif
				</div>

				@include('admin.budgets.partials.budget-items-customer')
			</div>
		</div>
	</div>
</div>

@endsection