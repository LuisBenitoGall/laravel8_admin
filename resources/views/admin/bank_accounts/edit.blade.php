@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Cuentas --}}
		<a href="{{ route('bank-accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_bancos_cuentas') !!}
		</a>

		{{-- Nueva cuenta --}}
		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('bank-accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('bank-accounts.update', $account) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Banco --}}
						<label for="bank_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.banco')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="bank_id" class="select2" label="banco_selec" :options="$banks" value="{{ $account->bank_id }}"/>
						</div>

						{{-- Cuenta principal --}}
						<label for="featured" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_principal')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
	                            <input type="checkbox" name="featured" @if($account->featured) checked @endif>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
	                    </div>

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
	                            <input type="checkbox" name="status" @if($account->status) checked @endif>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
	                    </div>
					</div>

					<div class="form-group row">
						{{-- Cuenta --}}
						<label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="account" autocomplete="off" maxlength="25" value="{{ $account->account }}"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection