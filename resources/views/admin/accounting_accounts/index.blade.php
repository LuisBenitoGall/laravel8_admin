@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('accounting-accounts.create')
			<a class="btn btn-info" href="{{ route('accounting-accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

{{-- Cuentas default --}}
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('accounting-accounts.default-accounts') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<p class="text-warning">{{ trans('textos.cuentas_contables_defecto_texto') }}</p>

					<div class="form-group row">
						{{-- Cuenta ventas --}}
						<label for="accounting_sales" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_ventas')) !!}
						</label>
						<div class="col-lg-4">
							{{-- @php
							$val1 = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_')]:'';
							@endphp
							<x-select name="accounting_sales" class="select2" :options="$accounting_accounts" label="cuenta_contable_selec" value="{{ $val1 }}"/> --}}

							<x-input type="text" name="accounting_sales_" datas="id:1" class="search_accounting_account" id="searchAccountingAccount1" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="accounting_sales" id="accountingAccountId1">
						</div>

						{{-- Cuenta compras --}}
						<label for="accounting_purchases" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_compras')) !!}
						</label>
						<div class="col-lg-4">
							{{-- @php
							$val2 = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_')]:'';
							@endphp
							<x-select name="accounting_purchases" class="select2" :options="$accounting_accounts" label="cuenta_contable_selec" value="{{ $val2 }}"/> --}}

							<x-input type="text" name="accounting_purchases_" datas="id:2" class="search_accounting_account" id="searchAccountingAccount2" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="accounting_purchases" id="accountingAccountId2">
						</div>
					</div>

					<div class="form-group row">
						{{-- Cuenta ventas IVA --}}
						<label for="accounting_sales_iva" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_ventas')) !!} IVA
						</label>
						<div class="col-lg-4">
							{{-- @php
							$val3 = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_IVA_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_IVA_')]:'';
							@endphp
							
							<x-select name="accounting_sales_iva" class="select2" :options="$accounting_accounts" label="cuenta_contable_selec" value="{{ $val3 }}"/> --}}

							<x-input type="text" name="accounting_sales_iva_" datas="id:3" class="search_accounting_account" id="searchAccountingAccount3" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="accounting_sales_iva" id="accountingAccountId3">
						</div>

						{{-- Cuenta compras IVA --}}
						<label for="accounting_purchases_iva" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_compras')) !!} IVA
						</label>
						<div class="col-lg-4">
							{{-- @php
							$val4 = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')]:'';
							@endphp
							<x-select name="accounting_purchases_iva" class="select2" :options="$accounting_accounts" label="cuenta_contable_selec" value="{{ $val4 }}"/> --}}

							<x-input type="text" name="accounting_purchases_iva_" datas="id:4" class="search_accounting_account" id="searchAccountingAccount4" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="accounting_purchases_iva" id="accountingAccountId4">
						</div>
					</div>

					<div class="form-group row">
						{{-- Cuenta caja ventas --}}
						<label for="accounting_cash_sales" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_ventas_caja')) !!}
						</label>
						<div class="col-lg-4">
							{{-- @php
							$val5 = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_SALES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_SALES_')]:'';
							@endphp
							<x-select name="accounting_cash_sales" class="select2" :options="$accounting_accounts" label="cuenta_contable_selec" value="{{ $val5 }}"/> --}}

							<x-input type="text" name="accounting_cash_sales_" datas="id:5" class="search_accounting_account" id="searchAccountingAccount5" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="accounting_cash_sales" id="accountingAccountId5">
						</div>

						{{-- Cuenta caja compras --}}
						<label for="accounting_cash_purchases" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_pagos_caja')) !!}
						</label>
						<div class="col-lg-4">
							{{-- @php
							$val6 = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')]:'';
							@endphp
							<x-select name="accounting_cash_purchases" class="select2" :options="$accounting_accounts" label="cuenta_contable_selec" value="{{ $val6 }}"/> --}}

							<x-input type="text" name="accounting_cash_purchases_" datas="id:6" class="search_accounting_account" id="searchAccountingAccount6" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" required/>

                            <input type="hidden" name="accounting_cash_purchases" id="accountingAccountId6">
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{!! trans('textos.cuentas_contables') !!} 
			<b>{{ $company->name }}</b> 
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccountingAccounts">
			<thead>
				<tr>
					<th class="wdt50">{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.nombre')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.empresa')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.tipo')) !!}</th>
					{{-- <th class="wdt100">{!! ucfirst(trans('textos.subtipo')) !!}</th> --}}
					<th class="text-center wdt50">{!! ucfirst(trans('textos.iva_aplica')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection