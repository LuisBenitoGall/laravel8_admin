@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('purchase-products.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_productos_contables') !!}
		</a>

		{{-- Nuevo producto - condiciones 
			1. Disponer de permisos de creación de producto.
			...
        --}}
		@can('purchase-products.create')
    		<a href="{{ route('purchase-products.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ ucfirst(trans('textos.producto_contable')) }} <u>{{ ucfirst($product->name) }}</u></h3>

				<form method="post" autocomplete="off" action="{{ route('purchase-products.update', $product) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre producto --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
						<div class="col-lg-8">
							<x-input type="text" name="name" autocomplete="off" maxlength="250" value="{{ $product->name }}" required/>
						</div>

						{{-- Estado --}}
						<label for="status" class="col-md-1 col-form-label text-md-right">{!! ucfirst(trans('textos.estado')) !!}</label>
                        <div class="col-md-1">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="status" @if($product->status) checked @endif>
                        		<span class="chk mt-1"></span>
                    		</label>
                        </div>
					</div>

					<div class="form-group row">
						{{-- Cuenta contable --}}
						<label for="accounting_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}</label>
						<div class="col-lg-6">
							@php
							$account_name = $account? $account->name:'';
							$account_id = $account? $account->id:'';
							@endphp
							{{-- <x-input type="text" name="accounting_account_" class="search_accounting_account" datas="id:1" id="searchAccountingAccount1" placeholder="{!! ucfirst(trans('textos.cuenta_contable')) !!}" maxlength="150" value="{{ $account_name }}"/>

							<input type="hidden" name="accounting_account" id="accountingAccountId1" value="{{ $account_id }}"> --}}

							<x-input type="text" name="accounting_account_" id="search_accounting_account" placeholder="{!! ucfirst(trans('textos.cuenta_contable')) !!}" maxlength="150" value="{{ $account_name }}"/>

							<input type="hidden" name="accounting_account" id="accountingAccountId" value="{{ $account_id }}">
						</div>

						{{-- IVA --}}
						<label for="iva" class="col-lg-2 col-form-label">IVA</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" value="{{ $product->iva }}" groupvalue="%"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" value="{{ $product->description }}"/>
						</div>
					</div>
							
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection