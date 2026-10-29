@if($items->count())
	@php
	$k = 1;
	@endphp

	<div id="briefing-items-list">
		@foreach($items as $it)
			<form action="#" class="bil-frm">
				{{ csrf_field() }}
				<input type="hidden" name="id" value="{{ $it->id }}">

				<div class="col-12 bil-item mb-2 py-2">
					<div class="row form-group mb-2">
						<div class="col-md-4 pt-2">
							<span class="bil-num">
								{{ $k }}.
							</span> 
							<label>{{ $it->concept }}</label>
						</div>
							
						{{-- Cantidad --}}
						<label for="qty" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.tiempo')) !!}</label>
						<div class="col-lg-1">
							<x-input type="text" name="qty" class="text-right" autocomplete="off" maxlength="6" value="{{ $it->time_qty }}"/>	
						</div>

						{{-- Unidad --}}
						<label for="unit" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.unidad')) !!}</label>
						<div class="col-lg-2">
							<x-select name="unit" class="" label="unidad_selec" :options="$time_units" value="{{ $it->time_unit }}"/>
						</div>

						{{-- Acciones --}}
						<div class="col-md-3 text-right">
							{{-- Guardar --}}
							<button href="#" class="btn btn-xs btn-success btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.guardar')) }}">	
								<i class="fa fa-check"></i>
							</button>

							{{-- Eliminar --}}
							<a href="#" class="btn btn-xs btn-danger btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.item_confirm_eliminar')) }}">	<i class="fa fa-trash"></i>
							</a>
						</div>
					</div>

					<div class="row form-group mb-0">
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" value="{!! $it->observations !!}"/>
						</div>
					</div>
				</div>
			</form>

			@php
			$k++;
			@endphp
		@endforeach
	</div>

@else
	<div class="row">
		<div class="col-12 pt-4 pb-5">
			<h3 class="text-warning text-center">{{ trans('textos.briefing_sin_items') }}</h3>
		</div>
	</div>
@endif