<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('companies.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="environment" value="true">
					<input type="hidden" name="guardian" value="{{ $guardian }}">

					@if($accounts)
						<input type="hidden" name="account_id" id="accountId" @if($accounts->count() == 1) value="{{ $accounts[0]->id }}" @endif>
					@endif

					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

					{{-- Para la creación de clientes o proveedores damos la opción de seleccionarlo directamente del listado de empresas existentes --}}
					@if($business_relationship && isset($companies))
						<div class="form-group row mb-4">
							<div class="col-md-10 offset-md-2">
								<p class="text-warning">{!! trans('textos.selec_empresa_crea_nueva') !!}</p>
							</div>

							{{-- Selector empresa --}}
							<label for="company_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa_selec')) !!} </label>
							<div class="col-lg-9">
								@php
                                $options = [];
                                @endphp

                                @foreach($companies as $company)
                                    @php
                                    $options[$company->id] = $company->name;
                                    @endphp
                                @endforeach

                                <x-select name="company_id" id="companyId" class="select2" label="empresa_selec" :options="$options" info="customer-provider-select"/>

				                <span class="invalid-feedback company_id" role="alert">
				                	<strong></strong>
				                </span>

				                @if(count($companies) == 0)
									<span class="text-danger"><strong>{!! ucfirst(trans('textos.empresas_vacio')) !!}</strong></span>
				                @endif
							</div>
						</div>	
						<hr><br>				
					@endif

					<div class="form-group row">
						{{-- Razón social de la empresa --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!} *</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" autocomplete="off" maxlength="150" required info="company-name"/>
						</div>	

						{{-- Nombre comercial --}}
						<label for="tradename" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" info="company-tradename"/>
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

                        	<x-input type="text" name="nif" id="nif" class="{{ $is_invalid }}" placeholder="NIF" autocomplete="off" maxlength="30" required info="company-nif"/>
                        </div>

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

@if($business_relationship && isset($companies))
	@once
	@push('scripts')
	<script>
	$(function(){
		// Campos obligatorios según se seleccione o no empresa:
	    $('#companyId').on('change', function(){
	        var valor = $(this).val();

	        if(valor != ''){
	            $('#name, #nif').prop('required', false);   
	        }else{
	            $('#name, #nif').prop('required', true);
	        }
	    });    
	});
	</script>
	@endpush
	@endonce						
@endif