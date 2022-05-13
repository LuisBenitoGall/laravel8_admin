<form method="post" autocomplete="off" action="{{ route('work-orders.update', $work_order) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de orden de trabajo --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.trabajo_orden_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
			{{-- Patrón de numeración - condiciones:
			 	1. Si existen patrones.
			 	2. No está declarado el número.
				...
			--}}
			@if($patterns && !$work_order->work_order_num)
				@if(count($patterns))
					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns" info="order-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($work_order->work_order_num)
						{{ $work_order->work_order_num }}
					@else
						{{ $work_order->id }}
					@endif
					</u></b></label>
			@endif
		</div>

		{{-- Orden de trabajo manual --}}
		<label for="work_order_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.num_manual')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="work_order_old_num" value="{{ $work_order->work_order_old_num }}" autocomplete="off" maxlength="150" info="order-old-num"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Título de la orden de trabajo --}}
		<label for="name" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}</label>
		<div class="col-md-5 col-lg-6">
			<x-input type="text" name="name" value="{{ $work_order->name }}" autocomplete="off" maxlength="255"/>
		</div>

		{{-- Estado pedido --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-lg-2">
			@php
			$estados = App\Helpers\helpers::workOrderStatus();
			@endphp

			<x-select name="status" class="select2" label="indica_estado" :options="$estados" value="{{ $work_order->status }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Fecha entrega prevista --}}
		<label for="estimated_delivery_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_entrega_prevista')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$estimatedDeliveryDate = $work_order->estimated_delivery_date != ''? \Carbon\Carbon::parse($work_order->estimated_delivery_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="estimated_delivery_date" id="estimated_delivery_date" value="{{ $estimatedDeliveryDate }}" :format="$languages[config('app.locale')][4]"/>
		</div>

		{{-- Fecha entrega --}}
		<label for="delivery_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_entrega')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$deliveryDate = $work_order->delivery_date != ''? \Carbon\Carbon::parse($work_order->delivery_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="delivery_date" id="delivery_date" value="{{ $deliveryDate }}" :format="$languages[config('app.locale')][4]"/>
		</div>

		{{-- Simulación --}}
		<label for="simulation" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.simulacion')) !!}</label>
		<div class="col-md-4 col-lg-2">
			<label class="custom-chk-rdb" rel="tooltip">
	            <input type="checkbox" name="simulation" @if($work_order->simulation) checked @endif>
	            <span class="chk" style="top: 5px;"></span>

	        	{!! App\Helpers\Helpers::info('work-order-simulation', true) !!}
	        </label>
		</div>
	</div>

	{{-- Descripción --}}
	<div class="form-group row">
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $work_order->description }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>
