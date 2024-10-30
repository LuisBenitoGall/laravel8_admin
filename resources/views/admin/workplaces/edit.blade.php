@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('workplaces.index', [$workplace->company_id, $business_relationship]) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_centros_trabajo') !!}
		</a>

		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('workplaces.create', [$workplace->company_id, $business_relationship]) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.centro_trabajo_nuevo')) !!}</a>
		@endcan

		<a href="#" class="btn btn-info" rel="tooltip" title="{!! ucfirst(trans('textos.telefonos')) !!}" data-toggle="modal" data-target="#modal-phones">
			<i class="fa fa-phone"></i>
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-4">{{ trans('textos.centro_trabajo_editar') }} <u>{{ $workplace->name }} ({{ $company->name }})</u></h3>	

				<form method="post" autocomplete="off" action="{{ route('workplaces.update', $workplace->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="id" value="{{ $workplace->id }}">
					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

					<div class="form-group row">
						{{-- Nombre centro de trabajo --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_trabajo')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="150" value="{{ $workplace->name }}" required/>
						</div>

						{{-- Status: sólo si no se trata del centro de trabajo principal --}}
						@if($workplace->featured != 1)
							<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_activo')) !!}</label>
							<div class="col-lg-2">
								<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
		                            <input type="checkbox" name="status" @if($workplace->status == 1) checked @endif>
		                            <span class="chk" style="top: 5px;"></span>
		                        </label>
		                    </div>
		                @else
		                	<input type="hidden" name="status" value="1">
		                @endif
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>	
						<div class="col-lg-10">
							<x-input-textarea name="description" value="{{ $workplace->description }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- País --}}
						@if(is_array($countries))
							<label for="country_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pais')) !!}</label>
							<div class="col-lg-4">
								@php
								$selected = $workplace->country_id? $workplace->country_id:NULL;
								@endphp

								<x-select name="country_id" id="country_id" class="select2" label="selec_pais" :options="$countries" value="{{ $selected }}"/>
							</div>
						@endif

						{{-- Provincia --}}
						<label for="province_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.provincia')) !!}</label>
						<div class="col-lg-4">
							@php
							$selected = $workplace->province_id? $workplace->province_id:NULL;
							@endphp

							<x-select name="province_id" id="province_id" class="select2" label="selec_provincia" :options="$provinces" value="{{ $selected }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Población --}}
						<label for="town_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.poblacion')) !!}</label>
						<div class="col-lg-4">
							@php
							$selected = $workplace->town_id? $workplace->town_id:NULL;
							@endphp

							<x-select name="town_id" id="town_id" class="select2" label="selec_poblacion" :options="$towns" value="{{ $selected }}"/>
						</div>

						{{-- Código Postal --}}
						<label for="cp" class="col-lg-2 col-form-label">CP</label>	
						<div class="col-lg-2">
							<x-input type="text" name="cp" id="cp" autocomplete="off" maxlength="10" value="{{ $workplace->cp }}"/>
						</div>
				    </div>

				    <div class="form-group row">
				    	{{-- Dirección --}}
						<label for="address" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.direccion')) !!}</label>	
						<div class="col-md-10 col-lg-6">
							<x-input type="text" name="address" id="GMautocomplete" autocomplete="off" maxlength="255" value="{{ $workplace->address }}"/>
						</div>

						{{-- NIF centro de trabajo --}}
						{{--
						<label for="nif" class="col-lg-2 col-form-label">NIF</label>	
						<div class="col-lg-2">
							<x-input type="text" name="nif" id="nif" autocomplete="off" maxlength="16" value="{{ $workplace->nif }}"/>
						</div>
						--}}
					</div>

					<div class="form-group row">
						{{-- Website --}}
						<label for="website" class="col-lg-2 col-form-label">Website</label>
						<div class="col-lg-4">
							<x-input type="text" name="website" autocomplete="off" maxlength="255" value="{{ $workplace->website }}"/>
						</div>

						{{-- Logo --}}
						<label for="name" class="col-lg-2 col-form-label">Logo</label>
						<div class="col-lg-4">
							@if($workplace->logo != NULL && file_exists('./img/workplaces/'.$workplace->logo))
								<div class="foto">
									<img class="img-fluid" src="{{ asset('img/workplaces/'.$workplace->logo) }}">	

									<div class="controls text-right pt-2">
										<a href="{{ route('workplaces.delete-logo', [$workplace->logo, $business_relationship]) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
											<i class="fa fa-trash"></i>
										</a>
									</div>
								</div>
							@else
								<x-input-file name="logo"/>

								{!! App\Helpers\Helpers::info('logo') !!}
							@endif
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
@include('admin.partials.modal-phones', ['workplace_id' => $workplace->id, 'phones' => $phones])

@endsection