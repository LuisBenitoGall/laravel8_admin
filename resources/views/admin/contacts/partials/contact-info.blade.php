@php
$readonly = $user->isAdmin == 1? true:false;
@endphp

<form method="post" autocomplete="off" action="{{ route('contacts.update', $contact) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Empresa --}}
		<label for="related_company" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
		<div class="col-lg-4">
			@if($related_company && $readonly)
				<x-input type="text" name="related_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" autocomplete="off" maxlength="150" required value="{{ $related_company->name }}" readonly="{{ $readonly }}"/>

			@else
				<x-input type="text" name="related_company" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150" value=""/>

				<input type="hidden" name="company_id" id="companyId" value="">
			@endif
		</div>
	</div>

	<div class="form-group row">
		{{-- Nombre usuario --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="75" required value="{{ $user->name }}" readonly="{{ $readonly }}"/>
		</div>

		{{-- Apellidos --}}
		<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="text" name="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="100" required value="{{ $user->surname }}" readonly="{{ $readonly }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Email --}}
		<label for="email" class="col-lg-2 col-form-label">Email*</label>
		<div class="col-lg-4">
			<x-input type="email" name="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="100" required value="{{ $user->email }}" readonly="{{ $readonly }}"/>
		</div>

		{{-- Posición --}}
		<label for="position" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.posicion')) !!}</label>
		<div class="col-lg-4">
			<x-input type="text" name="position" placeholder="{!! ucfirst(trans('textos.posicion')) !!}" autocomplete="off" maxlength="150" value="{{ $contact->position }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}*</label>
		<div class="col-lg-4">
			<x-select name="status" class="select2" :options="$status_options" required label="estado_selec" value="{{ $contact->status }}"/>
		</div>

		{{-- Contacto principal --}}
		<label for="is_primary" class="col-lg-2 col-form-label">{{ trans('textos.contacto_principal') }}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb">
                <input type="checkbox" name="is_primary" @if($contact->is_primary) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
        </div>
	</div>
	<br><br>

	{{-- Teléfonos --}}
	@include('admin.phones.partials.add-phones', ['phones' => $phones])

	<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
</form>