@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado pedidos --}}
		@can('orders.index')
			<a href="{{ route('orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_pedidos') !!}
			</a>
		@endcan

		{{-- Editar --}}
		@can('orders.edit')
			<a href="{{ route('orders.edit', $order) }}" class="btn btn-info">
				<i class="fa fa-edit"></i>
				{!! trans('textos.pedido_editar') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe pedido flotante: --}}
				{{-- Pedido simple --}}
				@if(!$order->is_group)
					@livewire('budget-amount', ['budget_amount' => $order_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => $order_amount_without_discount, 'environment' => 'orders'])

				{{-- Agrupación --}}
				@else
					@livewire('budget-amount', ['budget_amount' => $group_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => '', 'environment' => 'groups', 'bag_amount' => $order->bag_amount])
				@endif

				<h3 class="mb-3">
					@if($order->is_group)
						{!! ucfirst(trans('textos.agrupacion')) !!}
					@else
						@if($order->provider_id == session('currentCompany'))
							{!! ucfirst(trans('textos.venta')) !!}
						@elseif($order->customer_id == session('currentCompany'))
							{!! ucfirst(trans('textos.compra')) !!}
						@endif
					@endif
					<u>
						@if($order_type == 'sale')
							@if($order->order_num)
								{{ $order->order_num }}
							@else
								{{ $order->id }}
							@endif
						@else
							@if($order->purchase_num)
								{{ $order->purchase_num }}
							@elseif($order->purchase_old_num)
								{{ $order->purchase_old_num }}
							@else
								{{ $order->id }}
							@endif
						@endif

						@if($order->name)
							- {{ $order->name }}
						@endif
					</u>

					{{-- Pedido en agrupación --}}
					@if($order->parent && !$order->is_group)
						<small class="ml-3 text-warning">
							({{ trans('textos.agrupacion_en') }})
						</small>
					@endif
				</h3>

				{{-- Info --}}
				<div class="mb-3" style="width: 90%;">
					{{-- Cliente --}}
					{{-- @include('admin.orders.partials.data-customer') --}}

					{{-- Projectos del cliente --}}
					{{-- @if($order_type == 'sale' && $customer_projects)
						<label class="mr-5">
							{!! ucfirst(trans('textos.proyecto_cliente')) !!}:
							<b class="text-warning">{!! $customer_projects !!}</b>
						</label>
					@endif --}}

					{{-- Proveedor --}}
					@include('admin.orders.partials.data-provider')

					{{-- Cliente Final --}}
					@if($final_customer)
						<label class="mr-5">
							{!! ucfirst(trans('textos.cliente_final')) !!}:
							<b>{{ $final_customer->name }}</b>
						</label>
					@endif

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $order->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							@if($order->updated_at) {{ $order->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Autor --}}
					{{-- <label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}:
						<b>
							@if($author)
								{{ ucfirst($author->name) }} {{ ucfirst($author->surname) }}
							@endif
						</b>
					</label> --}}

					{{-- Manager --}}
					{{-- <label class="mr-5">
						{!! ucfirst(trans('textos.manager')) !!}:
						<b>
							@if($owner)
								{{ ucfirst($owner->name) }} {{ ucfirst($owner->surname) }}
							@endif
						</b>
					</label> --}}

					{{-- Comercial --}}
					{{-- <label class="mr-5">
						{!! ucfirst(trans('textos.comercial')) !!}:
						<b>
							@if($commercial)
								{{ ucfirst($commercial->name) }} {{ ucfirst($commercial->surname) }}
							@endif
						</b>
					</label> --}}

					{{-- Confirmación pedido --}}
					{{-- @if($confirmed_by)
						@php
						$customer_confirmation = $order->customer_confirmation != ''? \Carbon\Carbon::parse($order->customer_confirmation)->format($languages[session()->get('locale')][4]):'';
						@endphp
						<label class="mr-5 bg-success p-1">
							{!! ucfirst(trans('textos.confirmado_por')) !!}:
							<b>
								{{ $confirmed_by->name }} {{ $confirmed_by->surname }}, {{ $customer_confirmation }}
							</b>
						</label>
					@endif --}}

					{{-- Bolsa --}}
					@if($order->is_group && $order->bag_amount > 0)
						<label class="mr-5">
							{!! ucfirst(trans('textos.bolsa')) !!}:
							<b>
								{{ round($order->bag_amount, 2) }}{{ $currency->symbol }}
							</b>
						</label>
					@endif

					{{-- Importe --}}
					<label class="mr-5">
						@php
						$this_amount = $order->is_group? $group_amount:$order_amount;
						@endphp
						@livewire('budget-amount-mini', ['budget_amount' => $this_amount, 'currency' => $currency->symbol])
					</label>

					{{-- Descuento aplicado - condiciones:
						1. El pedido no debe estar facturado.
						...
					--}}
					@php
					$remove_discount = !$order->invoiced? true:false;
					@endphp
					@include('admin.orders.partials.data-discount-applied', ['return' => 'orders', 'remove_discount' => $remove_discount])

					{{-- Presupuesto --}}
					@if($order->provider_id == session('currentCompany'))
						@include('admin.orders.partials.link-budget')
					@endif

					{{-- Proyecto --}}
					{{-- @include('admin.orders.partials.link-project') --}}
				</div>

			</div>
		</div>
	</div>
</div>

@endsection