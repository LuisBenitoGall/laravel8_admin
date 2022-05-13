@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	@php
	$label = $business_relationship == 'customers'? 'volver_clientes':'volver_proveedores';
	@endphp
	<div class="col-sm-12">
		<a href="{{ route($business_relationship.'.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.'.$label) !!}
		</a>
	</div>
</div>

@include('admin.companies.partials.form-create', ['business_relationship' => $business_relationship])

@endsection
