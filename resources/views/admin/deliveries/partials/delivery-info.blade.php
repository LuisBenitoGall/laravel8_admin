<form method="post" autocomplete="off" action="{{ route('deliveries.update', $delivery) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de entrega --}}
		@php
		//Patrón requerido:
		$pattern_required = $patterns && $patterns->count() && session('companySettings')->pattern_deliveries? true:false;
		@endphp

		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">
			{!! ucfirst(trans('textos.entrega_num')) !!}
			@if($pattern_required)
				*
			@endif
		</label>
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

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" required="{{ $pattern_required }}" info="delivery-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($delivery->delivery_num)
						{{ $delivery->delivery_num }}
					@else
						{{ $delivery->id }}
					@endif
					</u></b></label>
			@endif

			{{-- Reset de número: 
				1. Sólo si los pedidos vinculados no están facturados 
				2. Sólo para el original, no para las versiones.  Estas se actualizan si cambia el número del original.
				3. Exclusivo para proveedor.
			--}}
			@if($delivery->delivery_num)
				<a href="{{ route('deliveries.reset-num', $delivery->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.numero_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Entrega nº manual --}}
		<label for="delivery_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.entrega_num_manual')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="delivery_old_num" value="{{ $delivery->delivery_old_num }}" autocomplete="off" maxlength="150" info="delivery-old-num"/>
		</div>

		{{-- Estado --}}
		<label for="status" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-select name="status" class="select2" label="estado_selec" :options="$status" value="{{ $delivery->status }}"/>
		</div>
	</div>




	{{-- Observaciones --}}
	<div class="form-group row">
		<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="observations" class="editor" value="{{ $delivery->observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>

