@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="companyId" value="{{ $company->id }}">
<input type="hidden" id="businessRelationship" value="{{ $business_relationship }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@if($business_relationship == 'companies')
			<a href="{{ route('companies.edit', $company) }}" class="btn btn-info">
			@php
			$label = 'volver_edicion_empresa';
			@endphp
		@else
			<a href="{{ route($business_relationship.'.edit', $company) }}" class="btn btn-info">
			@php
			$label = $business_relationship == 'customers'? 'volver_edicion_cliente':'volver_edicion_proveedor';
			@endphp	
		@endif
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.'.$label) !!}
		</a>

		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('cost-centers.create', [$company, $business_relationship]) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.centro_coste_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.centros_coste_de')) }} <u>{{ $company->name }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCostCenters">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.centro_coste')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection