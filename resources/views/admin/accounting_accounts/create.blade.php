@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('accounting-accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_cuentas_contables') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('accounting-accounts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Tipo --}}
						<label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="type" class="select2" id="aaType" :options="$types" label="tipo_selec" required/>
						</div>

						{{-- Subtipo --}}
						<label for="subtype" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.subtipo')) !!}</label>
						<div class="col-lg-4">
							<x-select name="subtype" class="select2" id="aaSubtype" label="subtipo_selec" :options="[]" disabled/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Empresa --}}
						<label for="company" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="company" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150"/>
							<input type="hidden" name="company_id" id="companyId">
						</div> 

						{{-- Cuenta interna --}}
						<label for="own_company" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_interna')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb">
								<input type="checkbox" name="own_company" id="chkInternalAccount">
								<span class="chk" style="top: 5px;"></span>
							</label>

							<div class="position-absolute" style="left: 65px; top: 0;">
	            				{!! App\Helpers\helpers::info('accounting-account-internal') !!}
	            			</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Cuenta --}}
						<label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="account" id="aaAccount" placeholder="{!! ucfirst(trans('textos.cuenta')) !!}" autocomplete="off" maxlength="{{ session('companySettings')->accounting_account_digits }}" disabled/>
						</div>

						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_nombre')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
								<input type="checkbox" name="status" checked>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div>

						{{-- Con IVA --}}
						<label for="with_iva" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.iva_aplica')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.iva_aplica') !!}">
								<input type="checkbox" name="with_iva" checked>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div>
					</div>	 
		
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
//Subtipos de cuentas contables:
var subtypes = {!! json_encode($subtypes) !!};

$(document).ready(function () {
	//Habilitar / deshabilitar campo de numeración de la cuenta:
	$('#chkInternalAccount').on('click', function(){
		$('#aaAccount').val('');
		if($(this).is(':checked')){
			$('#search_company, #companyId').val('');
			$('#aaAccount').prop('disabled', false);
		}else{
			$('#aaAccount').prop('disabled', true);
		}
	});

	//Selector subtipo cuenta bancaria.
	$('#aaType').on('change', function (event) {
        //Limpiamos posibles valores de provincia y población:
        $('#aaSubtype').empty().val('');
        $('#aaType option:selected').each(function () {
            var option = event.target.value;
           	var suboptions = subtypes[option];
           	console.log(suboptions);
        	if(typeof suboptions != 'undefined'){
                $('#aaSubtype').removeAttr('disabled');
                $("#aaSubtype").append('<option value="">' + txtSubtipoSelec + '</option>');
                for(var k in suboptions) {
                    $("#aaSubtype").append('<option value="' + k + '">' + suboptions[k] + '</option>');
                }
            }else{
            	$('#aaSubtype').attr('disabled', 'disabled');	
            }
        });
    });
});
</script>
@endpush

@endsection