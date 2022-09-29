@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('receiving.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_recepciones') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.recepcion')) !!} <u>{{ $delivery->id }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3" style="width: 90%;">
					{{-- Proveedor --}}
					@include('admin.orders.partials.data-provider')

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_envio')) !!}: 
						<b>
							{!! \Carbon\Carbon::parse($delivery->send_date)->format($languages[session()->get('locale')][4]) !!}
						</b>
					</label>

					{{-- Fecha recepción prevista --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.recepcion_fecha_prevista')) !!}: 
						<b>
							{!! \Carbon\Carbon::parse($delivery->estimated_reception_date)->format($languages[session()->get('locale')][4]) !!}
						</b>
					</label>

					{{-- Fecha recepción --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.recepcion_fecha')) !!}: 
						<b>
							{!! \Carbon\Carbon::parse($delivery->reception_date)->format($languages[session()->get('locale')][4]) !!}
						</b>
					</label>

					{{-- Albarán --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.albaran')) !!}: 
						<b>
							@if($delivery->delivery_num)
								{{ $delivery->delivery_num }}
							@else
								{{ $delivery->delivery_old_num }}
							@endif
						</b>
					</label>

					<br>
					{{-- Observaciones --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.observaciones')) !!}: 
						{!! $delivery->customer_observations !!}
					</label>
				</div>

				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.recepcion_cabecera')) !!}</a>
			  		</li>

			  		{{-- Pedidos de la recepción --}}
			  		@foreach($orders as $order)
			  			@php
			  			$order_num = $order->purchase_num? $order->purchase_num:($order->purchase_old_num? $order->purchase_old_num:$order->id);
			  			@endphp

			  			<li class="nav-item @if($tab == $order->id) active @endif">
			  				<a class="nav-link @if($tab == $order->id) active @endif" href="#tab-{{ $order->id }}" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedido')) !!} {{ $order_num }}</a>
			  			</li>	
			  		@endforeach
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.receivings.partials.receiving-info')	
					</div>

					{{-- Pedidos --}}
					@foreach($orders as $ord)
						<div class="tab-pane fade @if($tab == $ord->id) show active @endif" role="tabpanel" id="tab-{{ $ord->id }}">
							@include('admin.receivings.partials.receiving-order', ['ord_' => $ord])
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>

@endsection