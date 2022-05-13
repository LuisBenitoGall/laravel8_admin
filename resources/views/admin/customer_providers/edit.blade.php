@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		@php
		$text = $business_relationship == 'customers'? 'volver_clientes':'volver_proveedores';
		@endphp
		<a href="{{ route($business_relationship.'.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.'.$text) !!}
		</a>

		@include('admin.companies.partials.actions', ['company' => $company, 'business_relationship' => $business_relationship])
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