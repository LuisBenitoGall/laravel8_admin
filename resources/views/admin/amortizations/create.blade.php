@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a amortizaciones --}}
		<a href="{{ route('amortizations.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_amortizaciones') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card card-form">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('amortizations.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Código --}}
						<label for="code" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.codigo')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="code" maxlength="15" placeholder="{!! ucfirst(trans('textos.codigo')) !!}" autocomplete="off"/>
						</div>	

						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="description" maxlength="255" placeholder="{!! ucfirst(trans('textos.descripcion')) !!}" autocomplete="off" required/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Cuenta contable --}}
						<label for="amortization_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.amortizacion_cuenta')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="amortization_account" class="select2" :options="$accounting_accounts" label="cuenta_selec" required/>
						</div>

						{{-- Cuenta dota --}}
						<label for="amortization_dota" class="col-lg-2 col-form-label">Cuenta Dota*</label>
						<div class="col-lg-4">
							<x-select name="amortization_dota" class="select2" :options="$accounting_accounts" label="cuenta_selec" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Importe --}}
						<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!}*</label>
				<div class="col-lg-2">
				<x-input type="text" name="amount" class="text-right set-decimals" id="amount" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}" required/>
				</div>

				{{-- Porcentaje --}}
				<label for="amopor" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.porcentaje')) !!}*</label>
				<div class="col-lg-2">
				<x-input type="text" name="amopor" class="text-right set-decimals" id="amount" autocomplete="off" maxlength="8" group="right" groupvalue="%" required/>
				</div>

				{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				<input type="checkbox" name="status" checked>
				<span class="chk" style="top: 5px;"></span>
				</label>
						</div>
					</div>

					<div class="form-group row">
				{{-- Fecha --}}
				<label for="date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}*</label>
				<div class="col-md-4 col-lg-2">
					<x-date-picker name="date" :format="$languages[config('app.locale')][4]" required/>
				</div>
				</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection