@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-4"><u>{{ ucfirst($company->name) }}</u></h3>	

				<p class="text-warning mt-3">{!! trans('textos.configura_opciones_empresa') !!}</p>

				<form method="post" autocomplete="off" action="{{ route('company-settings.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="id" value="{{ $company_settings->id }}">

					{{-- Selectores de gestión --}}
					<div class="form-group row">
						{{-- Gestión clientes --}}
						<label for="customers_management" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.clientes_gestion')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="customers_management" @if($company_settings->customers_management == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>

				            <span class="suffix">
				            	{!! App\Helpers\helpers::info('company-settings-customers-management') !!}
				            </span>
						</div>

						{{-- Gestión proveedores --}}
						<label for="providers_management" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedores_gestion')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="providers_management" @if($company_settings->providers_management == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>

				            <span class="suffix">
				            	{!! App\Helpers\helpers::info('company-settings-providers-management') !!}
				            </span>
						</div>	

						{{-- Validación NIF --}}
						<label for="validate_nif" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.validar_nif')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="validate_nif" @if($company_settings->validate_nif == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>

				            <span class="suffix">
				            	{!! App\Helpers\Helpers::info('company-settings-validate-nif') !!}	
				            </span>
						</div>						
					</div>
					<hr>

					<div class="form-group row">
						{{-- Moneda --}}
						<label for="currency" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.moneda')) !!}</label>
						<div class="col-lg-3">
							@if(count($currencies) > 0)
								<x-select name="currency" class="select2" label="moneda_selec" :options="$currencies" value="{{ $company_settings->currency_id }}"/>
							@else
								<label class="text-warning pt-2">{!! trans('textos.monedas_no_definidas') !!}</label>
							@endif
						</div>
					</div>
					<hr>

					<div class="form-group row">
						{{-- IVA --}}
						<label for="iva" class="col-lg-2 col-form-label">IVA</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva" value="{{ $company_settings->iva }}" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
						</div>
					</div>
					<hr>

					<div class="form-group row">
						{{-- Emails corporativos --}}
						<label class="col-lg-2 col-form-label">{!! trans('textos.emails_corporativos') !!}</label>

						<div class="col-lg-1 pt-1">
							<button class="btn btn-warning btn-circle btnAddCoEmail" rel="tooltip" title="{{ trans('textos.email_agregar') }}">
								<i class="fa fa-plus"></i>
							</button>
						</div>

						<div class="col-lg-9">
							<div class="emails">
								@php
								$emails = $company_settings->emails? unserialize($company_settings->emails):false;
								@endphp

								@if($emails)
									@foreach($emails as $email)
										<div class="form-group row email">
											<div class="col-lg-5">
												<x-input type="text" name="emails[]" value="{{ $email }}"/>
											</div>

											<div class="col-lg-1 pt-1">
												<button class="btn btn-danger btn-circle btn-circle-mini btnRemoveCoEmail" rel="tooltip" title="{{ trans('textos.email_eliminar') }}">
													<i class="fa fa-times"></i>
												</button>
											</div>
										</div>
									@endforeach
								@endif
							</div>
						</div>
					</div>
					<hr>

					<div class="form-group row">
						{{-- Color principal --}}
						<label for="primary_color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color_principal')) !!}</label>
						<div class="col-lg-3">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon" @if($company_settings->primary_color) style="background-color: {{ $company_settings->primary_color }}" @endif>&nbsp;</span>
                				</div>

								<x-color-picker name="primary_color" id="colorpicker_primary_color" value="{{ $company_settings->primary_color }}"/>
							</div>
						</div>

						{{-- Color secundario --}}
						<label for="secondary_color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color_secundario')) !!}</label>
						<div class="col-lg-3">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon" @if($company_settings->secondary_color) style="background-color: {{ $company_settings->secondary_color }}" @endif>&nbsp;</span>
                				</div>

								<x-color-picker name="secondary_color" id="colorpicker_secondary_color" value="{{ $company_settings->secondary_color }}"/>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Color base presupuestos --}}
						<label for="base_color_budgets" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color_base_presupuestos')) !!}</label>
						<div class="col-lg-3">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon" @if($company_settings->base_color_budgets) style="background-color: {{ $company_settings->base_color_budgets }}" @endif>&nbsp;</span>
                				</div>

								<x-color-picker name="base_color_budgets" id="colorpicker_base_color_budgets" value="{{ $company_settings->base_color_budgets }}"/>
							</div>
						</div>

						{{-- Color base pedidos --}}
						<label for="base_color_orders" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color_base_pedidos')) !!}</label>
						<div class="col-lg-3">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon" @if($company_settings->base_color_orders) style="background-color: {{ $company_settings->base_color_orders }}" @endif>&nbsp;</span>
                				</div>

								<x-color-picker name="base_color_orders" id="colorpicker_base_color_orders" value="{{ $company_settings->base_color_orders }}"/>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Base color facturas --}}
						<label for="base_color_invoices" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color_base_facturas')) !!}</label>
						<div class="col-lg-3">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon" @if($company_settings->base_color_invoices) style="background-color: {{ $company_settings->base_color_invoices }}" @endif>&nbsp;</span>
                				</div>

								<x-color-picker name="base_color_invoices" id="colorpicker_base_color_invoices" value="{{ $company_settings->base_color_invoices }}"/>
							</div>
						</div>
					</div>
					<hr>

					<div class="form-group row">
						{{-- IP´s --}}
						<label class="col-lg-2 col-form-label">IP's</label>

						<div class="col-lg-1 pt-1">
							<button class="btn btn-warning btn-circle btnAddIp" rel="tooltip" title="{{ trans('textos.ip_agregar') }}">
								<i class="fa fa-plus"></i>
							</button>
						</div>

						<div class="col-lg-9">
							<div class="ips">
								@php
								$ips = $company_settings->ip? unserialize($company_settings->ip):false;
								@endphp

								@if($ips)
									@foreach($ips as $ip)
										<div class="form-group row ip">
											<div class="col-lg-4">
												<x-input type="text" name="ip[]" value="{{ $ip }}"/>
											</div>

											<div class="col-lg-1 pt-1">
												<button class="btn btn-danger btn-circle btn-circle-mini btnRemoveIp" rel="tooltip" title="{{ trans('textos.ip_eliminar') }}">
													<i class="fa fa-times"></i>
												</button>
											</div>
										</div>
									@endforeach
								@endif
							</div>
						</div>
					</div>
					<hr>

					{{-- Información pública --}}
					<div class="form-group row">
						<label class="col-lg-2 col-form-label">
							{{ trans('textos.info_publica') }}
							<br>
							<p class="text-warning">
								{{ trans('textos.info_publica_text') }}
							</p>
						</label>

						<div class="col-lg-10">
							<div class="row">
								@php
								$public_info = $company_settings->public_info? unserialize($company_settings->public_info):[];
								@endphp

								@foreach($public_items as $i => $item)
									<label for="public_info" class="col-lg-2 col-form-label">{!! ucfirst($item) !!}</label>
									<div class="col-lg-1">
										<label class="custom-chk-rdb" rel="tooltip">
							                <input type="checkbox" name="public_info[]" value="{{ $i }}" @if(in_array($i, $public_info)) checked @endif>
							                <span class="chk" style="top: 5px;"></span>
							            </label>
									</div>
								@endforeach
							</div>
						</div>
					</div>
					<hr>

					{{-- Contabilidad --}}
					<div class="form-group row">
						<label class="col-lg-2 col-form-label">
							{{ trans('textos.contabilidad') }}
						</label>

						<div class="col-lg-10">
							{{-- Dígitos cuentas contables --}}
							<div class="row">
								<label for="accounting_account_digits" class="col-lg-2 col-form-label">{{ trans('textos.cuenta_contable_digitos') }}</label>
								<div class="col-lg-1">
									<x-input type="text" name="accounting_account_digits" class="text-right" value="{{ $company_settings->accounting_account_digits }}" info="company-settings-accounting-account-digits"/>
								</div>
							

								{{-- Métodos contables --}}
								<label for="accounting_method" class="col-lg-2 col-form-label">{{ trans('textos.metodos_contables') }}</label>
								<div class="col-lg-3">
									<x-select name="accounting_method" class="select2" label="metodo_selec" :options="$accounting_methods" value="{{ $company_settings->accounting_method }}"/>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- HTML para clonar y añadir a previsions --}}
<div class="clones" style="display: none;">
	<div class="form-group row ip">
		<div class="col-lg-4">
			<x-input type="text" name="ip[]"/>
		</div>

		<div class="col-lg-1 pt-1">
			<button class="btn btn-danger btn-circle btn-circle-mini btnRemoveIp" rel="tooltip" title="{{ trans('textos.ip_eliminar') }}">
				<i class="fa fa-times"></i>
			</button>
		</div>
	</div>
</div>

<div class="clones-email" style="display: none;">
	<div class="form-group row email">
		<div class="col-lg-5">
			<x-input type="text" name="emails[]"/>	
		</div>

		<div class="col-lg-1 pt-1">
			<button class="btn btn-danger btn-circle btn-circle-mini btnRemoveCoEmail" rel="tooltip" title="{{ trans('textos.email_eliminar') }}">
				<i class="fa fa-times"></i>
			</button>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
    // Añadir email corporativo.
    $('.btnAddCoEmail').on('click', function(e){
        e.preventDefault(); 
        var email = $('.clones-email').html();
        $('.emails').append(email); 
        $('.fa-spinner').remove(); 
    });
    $(document).on('click', '.btnRemoveCoEmail', function(e){
        e.preventDefault();
        $(this).closest('.email').remove();
    });

    // Añadir ip.
    $('.btnAddIp').on('click', function(e){
        e.preventDefault(); 
        var ip = $('.clones').html();
        $('.ips').append(ip); 
        $('.fa-spinner').remove(); 
    });
    $(document).on('click', '.btnRemoveIp', function(){
        $(this).closest('.ip').remove();
    });
});
</script>
@endpush

@endsection