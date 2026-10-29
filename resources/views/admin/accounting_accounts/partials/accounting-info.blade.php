<form method="post" autocomplete="off" action="{{ route('accounting-accounts.update', $account) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Cuenta --}}
		<label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}*</label>
		<div class="col-lg-3">
			<x-input type="text" name="account" placeholder="{!! trans('textos.cuenta') !!}" autocomplete="off" required value="{{ $account->account }}" readonly/>
		</div>

		{{-- Nombre --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-3">
			<x-input type="text" name="name" placeholder="{!! trans('textos.nombre') !!}" autocomplete="off" required value="{{ $account->name }}"/>
		</div>

		{{-- Status --}}
		<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
				<input type="checkbox" name="status" @if($account->status == 1) checked @endif>
				<span class="chk" style="top: 5px;"></span>
			</label>
		</div>
	</div>	 

	{{-- <div class="form-group row"> --}}
		{{-- Tipo --}}
		{{-- <label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}*</label>
		<div class="col-lg-3">
			<x-select name="type" class="select2" :options="$types" label="tipo_selec" required value="{{ $account->type }}" disabled/>
		</div> --}}

		{{-- Empresa --}}
		{{-- <label for="company" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
		<div class="col-lg-5 position-relative">
			<x-input type="text" name="company" autocomplete="off" required value="{{ $company->name }}" disabled/>
		</div> 
	</div>  --}}

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>