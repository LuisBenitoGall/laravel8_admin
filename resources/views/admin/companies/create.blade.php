@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('companies.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_empresas') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('companies.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="environment" value="true">
					<input type="hidden" name="guardian" value="{{ $guardian }}">
					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

					@if($accounts)
						<input type="hidden" name="account_id" id="accountId" @if($accounts->count() == 1) value="{{ $accounts[0]->id }}" @endif>
					@endif

					<div class="form-group row">
						{{-- Razón social de la empresa --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!} *</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" autocomplete="off" maxlength="150" required info="company-name"/>
						</div>	

						{{-- Nombre comercial --}}
						<label for="tradename" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" required info="company-tradename"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- NIF --}}
						<label for="nif" class="col-md-2 col-form-label text-md-right">NIF *</label>
                        <div class="col-md-4">
                        	@php
                        	$is_invalid = '';
                        	@endphp
                        	@error('nif')
                        		@php
                        		$is_invalid = 'is-invalid';
                        		@endphp
                        	@enderror

                        	<x-input type="text" name="nif" class="{{ $is_invalid }}" placeholder="NIF" autocomplete="off" maxlength="30" required info="company-nif"/>
                        </div>

                        {{-- UTE --}}
                        <label for="is_ute" class="col-md-2 col-form-label text-md-right">UTE</label>
                        <div class="col-md-4 pt-1">
                        	<label class="custom-chk-rdb">
					        	<input type="checkbox" name="is_ute" id="is_ute">
					        	<span class="chk"></span>
					    	</label>

					    	<div class="position-absolute" style="left: 65px; top: 0;">
			            		{!! App\Helpers\helpers::info('company-ute') !!}
			            	</div>
                        </div>
                    </div>

                    {{-- Fechas de inicio y fin de la UTE --}}
                    <div id="isUte">
	                    <div class="form-group row">
	                		{{-- Fecha constitución --}}
							<label for="ute_start_at" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_constitucion')) !!}</label>
							<div class="col-lg-3">
								<x-date-picker name="ute_start_at" id="ute_start_at" :format="$languages[config('app.locale')][4]" info="ute-start-at"/>
							</div>

							{{-- Fecha constitución --}}
							<label for="ute_finish_at" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_final')) !!}</label>
							<div class="col-lg-3">
								<x-date-picker name="ute_finish_at" id="ute_finish_at" :format="$languages[config('app.locale')][4]" info="ute-finish-at"/>
							</div>
	                    </div>
                    </div>

                    <div class="form-group row">
                        {{-- Logo --}}
                        <label for="name" class="col-md-2 col-form-label">Logo
							<br>
							<small class="text-warning">
								· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
								· {{ trans('textos.tamany_max') }}: 1MB	<br>
								· {{ trans('textos.medidas_recomendadas') }}: 400x400px							
							</small>
						</label>
						<div class="col-md-4">
							<x-input-file name="logo"/>

							@if($errors->has('logo'))
			                    <span class="invalid-feedback" role="alert">
			                      <strong>{{ $errors->first('logo') }}</strong>
			                    </span>
			                @endif
						</div>
					</div>

					{{-- Cuenta de la empresa --}}
					@if($accounts && $accounts->count() > 1)
                        <div class="form-group row">
                            @foreach($accounts as $account)
                                <div class="card col-ms-6 col-md-4 mb-3 mt-3 account" data-id="{{ $account->id }}">    
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $account->name }}</h5>   

                                        <p class="card-text">{{ $account->rate }}{{ config('constants.CURRENCY_') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Inputs para UTEs.
	$('#is_ute').on('change', function(){
		if($(this).is(':checked')){
			$('#isUte').slideDown();
		}else{
			$('#isUte').slideUp();	
			$('#ute_start_at, #ute_finish_at').val('');
		}
	});
});
</script>
@endpush

@endsection
