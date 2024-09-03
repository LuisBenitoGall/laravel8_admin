<input type="hidden" name="id" value="{{ $company->id }}">

@php
//Camnpos inactivos para:
// 1. Agencias de transporte.
$readonly_ = isset($is_agency) && $is_agency? true:false;
@endphp

<div class="form-group row">
	{{-- Razón social de la empresa --}}
	<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!} *</label>
	<div class="col-lg-4">
		<x-input type="text" name="name" id="name" value="{{ $company->name }}" readonly="{{ $readonly_ }}" autocomplete="off" maxlength="150" required info="company-name"/>
	</div>	

	{{-- Nombre comercial --}}
	<label for="tradename" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
	<div class="col-lg-4">
		<x-input type="text" name="tradename" value="{{ $company->tradename }}" readonly="{{ $readonly_ }}" autocomplete="off" maxlength="150" info="company-tradename"/>
	</div>	
</div>

<div class="form-group row">
	{{-- NIF --}}
	<label for="nif" class="col-md-2 col-form-label text-md-right">NIF *</label>
    <div class="col-md-4">
    	@php
    	$is_invalid = '';
    	@endphp
    	@error('nif')
    		@php
    		$is_invalid = 'is-invalid';
    		@endphp
    	@enderror

    	<x-input type="text" name="nif" class="{{ $is_invalid }}" value="{{ $company->nif }}" readonly="{{ $readonly_ }}" autocomplete="off" maxlength="14" required info="company-nif"/>
    </div>

    {{-- CCC --}}
    <label for="ccc" class="col-md-2 col-form-label text-md-right">CCC</label>
    <div class="col-md-4">
    	<x-input type="text" name="ccc" value="{{ $company->ccc }}" autocomplete="off" maxlength="50" info="company-ccc"/>
    </div>
</div>

@if($company->is_ute)
	<div class="form-group row">
		{{-- Fecha constitución --}}
		<label for="ute_start_at" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_constitucion')) !!}</label>
		<div class="col-lg-3">
			@php
			$ute_start_at = $company->ute_start_at != ''? Carbon\Carbon::parse($company->ute_start_at)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="ute_start_at" id="ute_start_at" :format="$languages[config('app.locale')][4]" info="ute-start-at" value="{{ $ute_start_at }}"/>
		</div>

		{{-- Fecha constitución --}}
		<label for="ute_finish_at" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_final')) !!}</label>
		<div class="col-lg-3">
			@php
			$ute_finish_at = $company->ute_finish_at != ''? Carbon\Carbon::parse($company->ute_finish_at)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="ute_finish_at" id="ute_finish_at" :format="$languages[config('app.locale')][4]" info="ute-finish-at" value="{{ $ute_finish_at }}"/>
		</div>
    </div>
@endif

<div class="form-group row">
	{{-- Siglas --}}
	<label for="acronym" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.siglas')) }}</label>
	<div class="col-lg-4">
		<x-input type="text" name="acronym" id="acronym" value="{{ $company->acronym }}" autocomplete="off" maxlength="5" info="company-acronym"/>
	</div>

	{{-- Logo --}}
	<label for="logo" class="col-lg-2 col-form-label">Logo
		<br>
		<small class="text-warning">
			· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
			· {{ trans('textos.tamany_max') }}: 1MB	<br>
			· {{ trans('textos.medidas_recomendadas') }}: 400x400px							
		</small>
	</label>
	<div class="col-lg-4">
		@if($company->logo != NULL && file_exists('./img/companies/'.$company->logo))
			<div class="foto">
				<img class="img-fluid" src="{{ asset('img/companies/'.$company->logo) }}">	

				<div class="controls text-right pt-2">
					{{-- Eliminar --}}
					<a href="{{ route('companies.delete-logo', [$company->logo, $business_relationship]) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
						<i class="fa fa-trash"></i>
					</a>
				</div>
			</div>
		@else
			<x-input-file name="logo"/>
		@endif
	</div>
</div>

@if(isset($provider_workplaces) && count($provider_workplaces))
	<div class="form-group row">
		{{-- Centro homologado: centro de trabajo vinculado por defecto --}}
		<label for="provider_workplace_id" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.centro_homologado')) }}</label>
		<div class="col-lg-4">
			<x-select name="provider_workplace_id" class="select2" label="centro_trabajo_selec" :options="$provider_workplaces" info="workplace-approved" value="{{ $conditions->provider_workplace_id }}"/>
		</div>
	</div>
@endif

<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>