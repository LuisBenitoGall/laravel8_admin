<form method="post" autocomplete="off" action="{{ route('shipments.update', $shipment) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
		
	<div class="form-group row">
		{{-- Nº de reparto --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.reparto_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
			{{-- Patrón de numeración - condiciones:
			 	1. Si existen patrones.
				...
			--}}
			@if($patterns)
				@if($patterns->count())
					@php
					$patterns_ = [];
					foreach($patterns as $ptn){
						$patterns_[$ptn->id] = $ptn->name;
					}
					@endphp

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="shipment-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($shipment->shipment_num)
						{{ $shipment->shipment_num }}
					@else
						{{ $shipment->id }}
					@endif
					</u></b></label>
			@endif

			{{-- Reset de número: 
				1. Sólo si los pedidos vinculados no están facturados 
				2. Sólo para el original, no para las versiones.Estas se actualizan si cambia el número del original.
				3. Exclusivo para proveedor.
			--}}
			@if($shipment->shipment_num)
				<a href="{{ route('shipments.reset-num', $shipment->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.numero_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Reparto nº manual --}}
		<label for="shipment_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.reparto_num_manual')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="shipment_old_num" value="{{ $shipment->shipment_old_num }}" autocomplete="off" maxlength="150" info="shipment-old-num"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Estado --}}
		<label for="status" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-select name="status" class="select2" label="estado_selec" :options="$status" value="{{ $shipment->status }}"/>
		</div>

		{{-- Fecha --}}
		<label for="date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}</label>
		<div class="col-lg-2">
			<x-date-picker name="date" :format="$languages[config('app.locale')][4]" value="{{ $delivery_date }}"/>
		</div>	
	</div>

	<div class="form-group row">
		{{-- Vehículo --}}
		<label for="vehicle_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.vehiculo')) !!}</label>
		<div class="col-lg-4">
			<x-select name="vehicle_id" class="select2" label="vehiculo" :options="$vehicles" value="{{ $shipment->vehicle_id }}"/>
		</div>

		{{-- Repartidores --}}
		<label for="dealers" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.repartidores')) !!}</label>
		<div class="col-lg-4">
			<x-select name="dealers[]" class="select2" :options="$dealers" :values="$selected_dealers" multiple="true"/>
		</div>
	</div>

	{{-- Observaciones --}}
	<div class="form-group row">
		<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="observations" class="editor" value="{{ $shipment->observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" classdiv="col-12 text-right" label="{{ trans('textos.guardar') }}"/> 
</form>