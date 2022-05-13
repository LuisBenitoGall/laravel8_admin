@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('deliveries.index')
			<a href="{{ route('deliveries.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_entregas') !!}
			</a>
		@endcan

		{{-- Nueva entrega --}}
		@can('deliveries.create')
			<a href="{{ route('deliveries.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.entrega_nueva')) !!}
			</a>
		@endcan

		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-order">
			<i class="fa fa-plus"></i>
			{!! ucfirst(trans('textos.pedido_adjuntar')) !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.entrega')) !!} 
					<u>
						@php
						$num = $delivery->delivery_num? $delivery->delivery_num:($delivery->delivery_old_num? $delivery->delivery_old_num:$delivery->id);
						@endphp

						{!! $num !!}
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

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $delivery->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $delivery->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'orders') active @endif">
			  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.deliveries.partials.delivery-info')	
					</div>

					{{-- Pedidos: --}}
					<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-orders">
						@include('admin.deliveries.partials.delivery-orders')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Add order --}}
@include('admin.deliveries.partials.modal-add-order')

@endsection