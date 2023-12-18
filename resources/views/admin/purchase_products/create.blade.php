@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('purchase-products.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_productos_contables') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('purchase-products.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre producto --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
						<div class="col-lg-8">
							<x-input type="text" name="name" autocomplete="off" maxlength="250" required/>
						</div>

						{{-- Estado --}}
						<label for="status" class="col-md-1 col-form-label text-md-right">{!! ucfirst(trans('textos.estado')) !!}</label>
                        <div class="col-md-1">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="status" checked>
                        		<span class="chk mt-1"></span>
                    		</label>
                        </div>
					</div>

					<div class="form-group row">
						{{-- Cuenta contable --}}
						<label for="accounting_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}</label>
						<div class="col-lg-6">
							<x-input type="text" name="accounting_account_" class="search_accounting_account" datas="id:1" id="searchAccountingAccount1" placeholder="{!! ucfirst(trans('textos.cuenta_contable')) !!}" maxlength="150"/>

							<input type="hidden" name="accounting_account" id="accountingAccountId1">
						</div>

						{{-- IVA --}}
						<label for="iva" class="col-lg-2 col-form-label">IVA</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description"/>
						</div>
					</div>
							
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection