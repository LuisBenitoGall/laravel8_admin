<form method="post" autocomplete="off" action="{{ route('vehicles.update', $vehicle) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Vehículo --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.vehiculo')) !!}*</label>
		<div class="col-lg-7">
			<x-input type="text" name="name" value="{{ $vehicle->name }}" autocomplete="off" maxlength="255" required/>
		</div>

		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb">
                <input type="checkbox" name="status" @if($vehicle->status) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
		</div>
	</div>
	
	<div class="form-group row">
		{{-- Selecciona tipo --}}
		<label for="ype" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}</label>
		<div class="col-lg-4">
			<x-select name="type" class="select2" label="vehiculo_tipo_selec" :options="$types" value="{{ $vehicle->vehicle_type }}"/>
		</div>

		{{-- Matrícula --}}
		<label for="car_license" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.matricula')) !!}</label>
		<div class="col-lg-4">
			<x-input type="text" name="car_license" value="{{ $vehicle->car_license }}" autocomplete="off" maxlength="25"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Selecciona marca --}}
		<label for="brand" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.marca')) !!}</label>
		<div class="col-lg-4">
			<x-select name="brand" class="select2" label="marca_selec" :options="$brands" value="{{ $vehicle->brand }}"/>
		</div>

		{{-- Modelo --}}
		<label for="model" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.modelo')) !!}</label>
		<div class="col-lg-4">
			<x-input type="text" name="model" value="{{ $vehicle->model }}" autocomplete="off" maxlength="150"/>
		</div>						
	</div>

	<div class="form-group row">
		{{-- Nº bastidor --}}
		<label for="chassis_number" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.num_bastidor')) !!}</label>
		<div class="col-lg-4">
			<x-input type="text" name="chassis_number" value="{{ $vehicle->chassis_number }}" autocomplete="off" maxlength="100"/>
		</div>

		{{-- Fecha alta --}}
		<label for="discharge_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_alta')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$discharge_date = $vehicle->discharge_date != ''? \Carbon\Carbon::parse($vehicle->discharge_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="discharge_date" class="text-right" id="discharge_date" :format="$languages[config('app.locale')][4]" value="{{ $discharge_date }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Tara --}}
		<label for="base_weight" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.tara')) !!}</label>
		<div class="col-md-4 col-lg-2">
			<x-input type="text" name="base_weight" value="{{ $vehicle->vehicle_base_weight }}" class="text-right set-decimals" autocomplete="off" maxlength="11" group="right" groupvalue="Kg."/>
		</div>

		{{-- Peso máximo autorizado --}}
		<label for="max_weight" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.masa_maxima_autorizada')) !!}</label>
		<div class="col-md-4 col-lg-2">
			<x-input type="text" name="max_weight" value="{{ $vehicle->max_weight }}" class="text-right set-decimals" autocomplete="off" maxlength="11" group="right" groupvalue="Kg."/>
		</div>	

		{{-- Carga permitida --}}
		@if($vehicle->vehicle_base_weight > 0 && $vehicle->max_weight > 0)
			<label for="max_weight_" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.carga_max_permitida')) !!}</label>
			<div class="col-md-4 col-lg-2">
				<x-input type="text" name="max_weight_" value="{{ round($vehicle->max_weight - $vehicle->vehicle_base_weight, 2) }}" class="text-right set-decimals" disabled autocomplete="off" maxlength="11" group="right" groupvalue="Kg."/>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Observaciones --}}
		<label for="observations" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.observaciones')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="observations" class="editor" value="{{ $vehicle->observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>