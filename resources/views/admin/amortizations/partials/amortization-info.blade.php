<form method="post" autocomplete="off" action="{{ route('amortizations.update', $amortization) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Código --}}
		<label for="code" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.codigo')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="code" value="{{ $amortization->code }}" maxlength="15" placeholder="{!! ucfirst(trans('textos.codigo')) !!}" autocomplete="off"/>
		</div>	

		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}*</label>
		<div class="col-lg-6">
			<x-input type="text" name="description" value="{{ $amortization->description }}" maxlength="255" placeholder="{!! ucfirst(trans('textos.descripcion')) !!}" autocomplete="off" required/>
		</div>	
	</div>

	<div class="form-group row">
		{{-- Cuenta contable --}}
		<label for="amortization_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.amortizacion_cuenta')) !!}*</label>
		<div class="col-lg-4">
			<x-select name="amortization_account" class="select2" :options="$accounting_accounts" label="cuenta_selec" value="{{ $amortization->amortization_account }}" required/>
		</div>

		{{-- Cuenta dota --}}
		<label for="amortization_dota" class="col-lg-2 col-form-label">Cuenta Dota*</label>
		<div class="col-lg-4">
			<x-select name="amortization_dota" class="select2" :options="$accounting_accounts" label="cuenta_selec" value="{{ $amortization->amortization_dota }}" required/>
		</div>
	</div>
	<div class="form-group row">
		{{-- Importe --}}
		<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!}*</label>
<div class="col-lg-2">
<x-input type="text" name="amount" value="{{ $amortization->amount }}" class="text-right set-decimals" id="amount" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}" required/>
</div>

{{-- Amortizado --}}
<label for="amortized" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.amortizado')) !!}</label>
<div class="col-lg-2">
<x-input type="text" name="amortized" value="{{ $amortized }}" class="text-right set-decimals" id="amount" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}" disabled/>
</div>

{{-- Pendiente --}}
<label for="pendent" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pendiente')) !!}</label>
<div class="col-lg-2">
<x-input type="text" name="pendent" value="{{ round(($amortization->amount - $amortized), 4) }}" class="text-right set-decimals"autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}" disabled/>
</div>
	</div>

	<div class="form-group row">
{{-- Fecha --}}
<label for="date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}</label>
<div class="col-md-4 col-lg-2">
	@php
	$date = $amortization->date != ''? \Carbon\Carbon::parse($amortization->date)->format($languages[session()->get('locale')][4]):'';
@endphp 

	<x-date-picker name="date" :format="$languages[config('app.locale')][4]" value="{{ $date }}"/>
</div>

{{-- Porcentaje --}}
<label for="amopor" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.porcentaje')) !!}*</label>
<div class="col-lg-2">
<x-input type="text" name="amopor" value="{{ $amortization->amopor }}" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="%" required/>
</div>

{{-- Status --}}
		<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip">
<input type="checkbox" name="status" @if($amortization->status == 'A') checked @endif>
<span class="chk" style="top: 5px;"></span>
</label>
		</div>
</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>