@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('companies.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_empresas') !!}
		</a>

		@include('admin.companies.partials.actions', ['company' => $company, 'business_relationship' => $business_relationship])

		{{-- UTE --}}
		@if($company->is_ute)
			<a href="{{ route('company-utes.index', $company->id) }}" class="btn btn-info">
				<i class="fa fa-object-group"></i>
				{!! trans('textos.ute_empresas_vinculadas') !!}
			</a>
		@endif

		{{-- Asociar a empresa matriz - condiciones: 
			1. id y mother_co deben coincidir.
			2. No deben ser UTEs.
			...
		--}}
		@if($company->id == $company->mother_co && !$company->is_ute)
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-link-mother">
				<i class="fa fa-building"></i>
				{!! trans('textos.empresa_matriz_vincular') !!}
			</a>
		@endif

		{{-- Desvincular de empresa matriz - condiciones: 
			1. id y mother_co NO deben coincidir.
			2. No deben ser UTEs. 
		--}}
		@if($company->id != $company->mother_co && !$company->is_ute)
			<a href="{{ route('companies.unlink-mother', $company) }}" class="btn btn-danger btn-confirm" data-msg="{{ trans('textos.empresa_matriz_desvincular_confirm') }}">
				<i class="fa fa-unlink"></i>
				{!! trans('textos.empresa_matriz_desvincular') !!}
			</a>
		@endif

		{{-- Gestión de notificaciones: sólo para la empresa en session --}}
		@if(session('currentCompany') == $company->id)
			<a href="{{ route('company-notifications-management.index') }}" class="btn btn-info">
				<i class="fa fa-bell"></i>
				{!! trans('textos.notificaciones_gestion') !!}
			</a>
		@endif
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-4">
					<u>{{ ucfirst($company->name) }}</u>
					@if($mother)
						- {{ trans('textos.empresa_matriz') }} 
						<a target="_blank" href="{{ route('companies.edit', $mother->id) }}"><u>{{ $mother->name }}</u></a>
					@endif
				</h3>	

				<form method="post" autocomplete="off" action="{{ route('companies.update', $company->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">
					
					@include('admin.companies.partials.form-edit', ['company' => $company])
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Vincular empresa a matriz --}}
@include('admin.companies.partials.modal-link-mother')

@endsection