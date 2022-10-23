@extends('admin.partials.layout')

@section('content')

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


				<h2 class="text-warning text-center">En construcción</h2>
			</div>
		</div>
	</div>
</div>

@endsection