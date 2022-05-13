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
				<h3 class="mb-4"><u>{{ ucfirst($company->name) }}</u></h3>	

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

@endsection