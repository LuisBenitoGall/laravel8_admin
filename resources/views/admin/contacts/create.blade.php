@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('contacts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_contactos') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				@if($related_company)
					<div class="row">
						<h4 class="col-md-10 offset-2 mb-3">{{ trans('textos.contacto_nuevo_de') }} <u>{!! $related_company->name !!}</u></h4>
					</div>
				@endif

				{{-- Email sin verificar --}}
				@if(!$contact_email)
					<form method="post" autocomplete="off" action="{{ route('contacts.verify-email') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}
						@if($related_company)
							<input type="hidden" name="related_company" value="{{ $related_company->id }}">
						@endif

						<div class="row">
							<p class="text-warning col-md-6 offset-2">{{ trans('textos.email_verificar_texto') }}</p>
						</div>

						<div class="form-group row">
							{{-- Email --}}
							<label for="contact_email" class="col-lg-2 col-form-label">Email*</label>
							<div class="col-lg-6">
								<x-input type="email" name="contact_email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="100" required/>

								<x-input-errors name="email"/>	
								{{-- Pasamos a esta vista el error de validación del email por si se modifica durante la creación del contacto --}}
							</div>						

							<x-btn-submit name="submit_verify" label="validar" inline="true"/>
						</div>
					</form>

				{{-- Email verificado --}}
				@else
					{{-- Si el contacto existe y es admin no se pueden modificar sus datos --}}
					@php
					$readonly = $contact_exist && $contact_exist->isAdmin == 1? true:false;
					@endphp

					<form method="post" autocomplete="off" action="{{ route('contacts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}

						@if($contact_exist)
							<input type="hidden" name="user_id" value="{{ $contact_exist->id }}">
						@endif

						{{-- Empresa vinculada --}}
						@if($related_company)
							<input type="hidden" name="company_id" value="{{ $related_company->id }}">
						@else
							<div class="form-group row">
								{{-- Empresa --}}
								<label for="related_company" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
								<div class="col-lg-4">
									@php
									$company_name = $related_company? $related_company->name:'';
									$company_id = $related_company? $related_company->id:'';
									@endphp
									<x-input type="text" name="related_company" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150" value="{!! $company_name !!}"/>
								</div>

								<input type="hidden" name="company_id" id="companyId" value="{{ $company_id }}">	
							</div>
						@endif

						<div class="form-group row">
							{{-- Nombre usuario --}}
							@php
							$user_name = $contact_exist? $contact_exist->name:'';
							@endphp
							<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
							<div class="col-lg-4">
								<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="75" required value="{{ $user_name }}" readonly="{{ $readonly }}"/>
							</div>

							{{-- Apellidos --}}
							@php
							$user_surname = $contact_exist? $contact_exist->surname:'';
							@endphp
							<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
							<div class="col-lg-4">
								<x-input type="text" name="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="100" required value="{{ $user_surname }}" readonly="{{ $readonly }}"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Email --}}
							@php
							$user_email = $contact_exist? $contact_exist->email:($contact_email? $contact_email:'');
							@endphp
							<label for="email" class="col-lg-2 col-form-label">Email*</label>
							<div class="col-lg-4">
								<x-input type="email" name="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="100" required value="{{ $user_email }}" readonly="{{ $readonly }}"/>
							</div>

							{{-- Posición --}}
							<label for="position" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.posicion')) !!}</label>
							<div class="col-lg-4">
								<x-input type="text" name="position" placeholder="{!! ucfirst(trans('textos.posicion')) !!}" autocomplete="off" maxlength="150"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Status --}}
							<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}*</label>
							<div class="col-lg-4">
								<x-select name="status" class="select2" :options="$status_options" required label="estado_selec"/>
							</div>

							{{-- Contacto principal --}}
							<label for="is_primary" class="col-lg-2 col-form-label">{{ trans('textos.contacto_principal') }}</label>
							<div class="col-lg-1">
								<label class="custom-chk-rdb">
					                <input type="checkbox" name="is_primary">
					                <span class="chk" style="top: 5px;"></span>
					            </label>
					        </div>
						</div>
						<br><br>

						{{-- Teléfonos --}}
						@include('admin.phones.partials.add-phones', ['phones' => false])

						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
					</form>
				@endif
			</div>
		</div>	
	</div>
</div>

@endsection