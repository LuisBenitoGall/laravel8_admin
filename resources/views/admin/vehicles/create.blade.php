@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('vehicles.index')
			<a href="{{ route('vehicles.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_vehiculos') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('vehicles.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Vehículo --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.vehiculo')) !!}*</label>
						<div class="col-lg-7">
							<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb">
	                            <input type="checkbox" name="status" checked>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
						</div>
					</div>
					
					<div class="form-group row">
						{{-- Selecciona tipo --}}
						<label for="ype" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}</label>
						<div class="col-lg-4">
							<x-select name="type" class="select2" label="vehiculo_tipo_selec" :options="$types"/>
						</div>

						{{-- Matrícula --}}
						<label for="car_license" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.matricula')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="car_license" autocomplete="off" maxlength="25"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Selecciona marca --}}
						<label for="brand" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.marca')) !!}</label>
						<div class="col-lg-4">
							<x-select name="brand" class="select2" label="marca_selec" :options="$brands"/>
						</div>

						{{-- Modelo --}}
						<label for="model" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.modelo')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="model" autocomplete="off" maxlength="150"/>
						</div>						
					</div>

					<div class="form-group row">
						{{-- Nº bastidor --}}
						<label for="chassis_number" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.num_bastidor')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="chassis_number" autocomplete="off" maxlength="100"/>
						</div>

						{{-- Fecha alta --}}
						<label for="discharge_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_alta')) !!}</label>
						<div class="col-md-4 col-lg-2">
							<x-date-picker name="discharge_date" class="text-right" id="discharge_date" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Tara --}}
						<label for="base_weight" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.tara')) !!}</label>
						<div class="col-md-4 col-lg-2">
							<x-input type="text" name="base_weight" class="text-right set-decimals" autocomplete="off" maxlength="11" group="right" groupvalue="Kg."/>
						</div>

						{{-- Peso máximo autorizado --}}
						<label for="max_weight" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.masa_maxima_autorizada')) !!}</label>
						<div class="col-md-4 col-lg-2">
							<x-input type="text" name="max_weight" class="text-right set-decimals" autocomplete="off" maxlength="11" group="right" groupvalue="Kg."/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="observations" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.observaciones')) }}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" class="editor" />
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection