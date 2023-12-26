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
							@php
							$val1_text = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_')][1]:'';
							$val1_id = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_')][0]:'';
							@endphp

							<x-input type="text" name="accounting_sales_" datas="id:1" class="search_accounting_account" id="searchAccountingAccount1" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" value="{{ $val1_text }}"/>

                            <input type="hidden" name="accounting_sales" id="accountingAccountId1" value="{{ $val1_id }}">
						</div>

						{{-- Cuenta compras --}}
						<label for="accounting_purchases" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_compras')) !!}
						</label>
						<div class="col-lg-4">
							@php
							$val2_text = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_')][1]:'';
							$val2_id = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_')][0]:'';
							@endphp

							<x-input type="text" name="accounting_purchases_" datas="id:2" class="search_accounting_account" id="searchAccountingAccount2" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" value="{{ $val2_text }}"/>

                            <input type="hidden" name="accounting_purchases" id="accountingAccountId2" value="{{ $val2_id }}">
						</div>
					</div>

					<div class="form-group row">
						{{-- Cuenta ventas IVA --}}
						<label for="accounting_sales_iva" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_ventas')) !!} IVA
						</label>
						<div class="col-lg-4">
							@php
							$val3_text = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_IVA_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_IVA_')][1]:'';
							$val3_id = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_IVA_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_SALES_IVA_')][0]:'';
							@endphp

							<x-input type="text" name="accounting_sales_iva_" datas="id:3" class="search_accounting_account" id="searchAccountingAccount3" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" value="{{ $val3_text }}"/>

                            <input type="hidden" name="accounting_sales_iva" id="accountingAccountId3" value="{{ $val3_id }}">
						</div>

						{{-- Cuenta compras IVA --}}
						<label for="accounting_purchases_iva" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_compras')) !!} IVA
						</label>
						<div class="col-lg-4">
							@php
							$val4_text = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')][1]:'';
							$val4_id = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')][0]:'';
							@endphp

							<x-input type="text" name="accounting_purchases_iva_" datas="id:4" class="search_accounting_account" id="searchAccountingAccount4" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" value="{{ $val4_text }}"/>

                            <input type="hidden" name="accounting_purchases_iva" id="accountingAccountId4" value="{{ $val4_id }}">
						</div>
					</div>

					{{-- <div class="form-group row"> --}}
						{{-- Cuenta caja ventas --}}
						{{-- <label for="accounting_cash_sales" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_ventas_caja')) !!}
						</label>
						<div class="col-lg-4">
							@php
							$val5_text = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_SALES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_SALES_')][1]:'';
							$val5_id = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_SALES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_SALES_')][0]:'';
							@endphp

							<x-input type="text" name="accounting_cash_sales_" datas="id:5" class="search_accounting_account" id="searchAccountingAccount5" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250" value="{{ $val5_text }}"/>

                            <input type="hidden" name="accounting_cash_sales" id="accountingAccountId5" value="{{ $val5_id }}">
						</div> --}}

						{{-- Cuenta caja compras --}}
						{{-- <label for="accounting_cash_purchases" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.cuenta_pagos_caja')) !!}
						</label>
						<div class="col-lg-4">
							@php
							$val6_text = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')][1]:'';
							$val6_id = isset($default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')])? $default_accounts[config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')][0]:'';
							@endphp

							<x-input type="text" name="accounting_cash_purchases_" datas="id:6" class="search_accounting_account" id="searchAccountingAccount6" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="250"  value="{{ $val6_text }}"/>

                            <input type="hidden" name="accounting_cash_purchases" id="accountingAccountId6" value="{{ $val6_id }}">
						</div>
					</div> --}}

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
					<th class="wdt100">{!! ucfirst(trans('textos.modalidad')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.iva_aplica')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection