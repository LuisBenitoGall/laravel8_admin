@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="shipmentId" value="{{ $shipment->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a envíos --}}
		@can('shipments.index')
			<a href="{{ route('shipments.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_repartos') !!}
			</a>
		@endcan

		{{-- Nuevo envío --}}
		@can('shipments.create')
			<a class="btn btn-info" href="{{ route('shipments.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.reparto_nuevo')) !!}</a>
		@endcan

		{{-- Imprimir --}}
		<a href="{{ route('shipments.print', $shipment) }}" class="btn btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.imprimir')) }}">
			<i class="fa fa-print"></i>
			{!! ucfirst(trans('textos.imprimir')) !!}
		</a>

		{{-- Generar albaranes --}}
		<a class="btn btn-warning btnSetDeliveries btn-confirm" data-msg="Confirma que deseas generar los albaranes de los pedidos seleccionados." style="display: none;" href="{{ route('shipments.edit', [$shipment, 'deliveries']) }}"><i class="fa fa-file-export"></i> {!! ucfirst(trans('textos.albaranes_generar')) !!}</a>

		{{-- Nuevo reparto - condiciones: 
			1. Si existen albaranes asociados.
		--}}
		@if($deliveries_in->count())
			<a href="{{ route('shipments.reasign', $shipment) }}" class="btn btn-info btn-confirm" data-msg="Se reasignarán los albaranes no validados a un nuevo reparto.">
				<i class="fa fa-retweet"></i>
				{!! ucfirst(trans('textos.reparto_reasignar')) !!}
			</a>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.reparto')) !!}
					<u>
						@php
						$num = $shipment->shipment_num? $shipment->shipment_num:($shipment->shipment_old_num? $shipment->shipment_old_num:$shipment->id);
						@endphp

						{!! $num !!}
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha de entrega --}}
					<label class="mr-5">
						@php
						$delivery_date = $shipment->scheduled_date != ''? \Carbon\Carbon::parse($shipment->scheduled_date)->format($languages[session()->get('locale')][4]):'';
						@endphp
						{!! ucfirst(trans('textos.fecha_entrega')) !!}:
						<b>{{ $delivery_date }}</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}:
						<b>
							@if($author) {{ ucfirst($author->name) }} {{ ucfirst($author->surname) }} @endif
						</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							@if($shipment->created_at) {{ $shipment->created_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
						@if($shipment->updated_at) {{ $shipment->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
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

					<li class="nav-item @if($tab == 'deliveries') active @endif">
						<a class="nav-link @if($tab == 'deliveries') active @endif" href="#tab-deliveries" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.albaranes')) !!}</a>
					</li>
				</ul>

				{{-- Panels --}}
				<div class="tab-content pt-4">
					{{-- Cabecera: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.shipments.partials.shipment-info')
					</div>

					{{-- Pedidos: --}}
					<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-orders">
						@include('admin.shipments.partials.shipment-orders')
					</div>

					{{-- Albaranes: --}}
					<div class="tab-pane fade @if($tab == 'deliveries') show active @endif" role="tabpanel" id="tab-deliveries">
						@include('admin.shipments.partials.shipment-deliveries')
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Marcar - desmarcar pedido en reparto.
	$(document).on('click', '.chkSelOrder', function(){
		var elem = $(this);
		var id = $(this).data('id');	
		var shipment = $('#shipmentId').val();
		$.get("/admin/shipment-orders/" + id + "/store-ajax/" + shipment + "", function (response, state) {
			if (response) {
				$('.check-ko').remove();
				elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
				$('.check-ok').delay(1000).fadeOut();
			} else {
				elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
			}

			var num_deliveries = $(".chkSelOrder:checked").length;
			if(num_deliveries > 0){
				$('.btnSetDeliveries').fadeIn();	
			}else{
				$('.btnSetDeliveries').fadeOut();	
			}
		});
	});
});
</script>
@endpush

@endsection
