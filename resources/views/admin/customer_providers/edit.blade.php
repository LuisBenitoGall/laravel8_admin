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

		{{-- Nueva cuenta bancaria --}}
		@can('companies.edit')
			@if(!$company_account)
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-bank-account"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.banco_cuenta_nueva')) !!}</a>	
			@endif
		@endcan
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
						@php
						$tab_label = $business_relationship == 'providers'? 'proveedor_info':'cliente_info';
						@endphp
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.'.$tab_label)) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'users') active @endif">
			  			<a class="nav-link @if($tab == 'users') active @endif" href="#tab-users" role="tab" data-toggle="tab">{{ ucfirst(trans('textos.usuarios')) }}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'banks') active @endif">
			  			<a class="nav-link @if($tab == 'banks') active @endif" href="#tab-banks" role="tab" data-toggle="tab">{{ ucfirst(trans('textos.bancos_cuentas')) }}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'conditions') active @endif">
			  			<a class="nav-link @if($tab == 'conditions') active @endif" href="#tab-conditions" role="tab" data-toggle="tab">@if($business_relationship == 'customers') {{ ucfirst(trans('textos.condiciones_cobro')) }} @else {{ ucfirst(trans('textos.condiciones_pago')) }} @endif</a>
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

					{{-- Datos bancarios --}}
					<div class="tab-pane fade @if($tab == 'banks') show active @endif" role="tabpanel" id="tab-banks">
						@include('admin.customer_providers.partials.customer-providers-banks')
					</div>

					{{-- Condiciones de pago --}}
					<div class="tab-pane fade @if($tab == 'conditions') show active @endif" role="tabpanel" id="tab-conditions">
						@include('admin.customer_providers.partials.customer-providers-pay-conditions')
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>		

{{-- Modals --}}
{{-- Nueva cuenta bancaria: --}}
@include('admin.bank_accounts.partials.modal-new-bank-account', ['company_' => $company])

{{-- Editar cuenta bancaria: --}}
@include('admin.bank_accounts.partials.modal-edit-bank-account', ['company_' => $company])

@push('scripts')
<script>
$(function(){
	//Modal edición cuenta bancaria:
	$(document).on('click', '.btnEdit', function(){
		var id = $(this).data('id');
		var account = $(this).data('account');
		var bank = $(this).data('bank');

		$('#accountId').val(id);
		$('#accountNumber').val(account);
		$('#accountBank').val(bank).trigger('change');
	});
});
</script>
@endpush

@endsection