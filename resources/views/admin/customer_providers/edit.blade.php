@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtCompany" value="{{ $company->id }}">

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
				<h3 class="mb-3"><u>{{ ucfirst($company->name) }}</u></h3>	

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedido_cabecera')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'users') active @endif">
			  			<a class="nav-link @if($tab == 'users') active @endif" href="#tab-users" role="tab" data-toggle="tab">{{ ucfirst(trans('textos.usuarios')) }}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.customer_providers.partials.customer-providers-info')
					</div>

					{{-- Usuarios --}}
					<div class="tab-pane fade @if($tab == 'users') show active @endif" role="tabpanel" id="tab-users">
						@include('admin.customer_providers.partials.customer-providers-users')
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>					

@endsection