@extends('admin.partials.layout')

@section('content')

@php
//Cantidad de dígitos de la cuenta:
$maxlength = $company_settings->accounting_account_digits && $company_settings->accounting_account_digits > 0? $company_settings->accounting_account_digits:config('constants.ACCOUNTING_ACCOUNT_DIGITS_DEFAULT_');
@endphp

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
						{{-- Nombre de la cuenta --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_nombre')) !!}*</label>
						<div class="col-lg-3">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.cuenta_nombre')) !!}" autocomplete="off" required/>
						</div>

						{{-- Activos / Pasivos --}}
						<label for="type" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}*</label>
						<div class="col-lg-2">
							@php
							$tipos = [
								'a' => trans('textos.activos'),
								'p' => trans('textos.pasivos')
							]
							@endphp
							<x-select name="type" class="select2" :options="$tipos" label="tipo_selec" required/>
						</div>
										
						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
								<input type="checkbox" name="status" checked>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div>

						{{-- Con IVA --}}
						{{-- 8/11/2023: para ADN oculto por ahora esta opción, y se le pasa valor true por defecto --}}
						<input type="hidden" name="with_iva" value="true">
						{{-- <label for="with_iva" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.iva_aplica')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.iva_aplica') !!}">
								<input type="checkbox" name="with_iva" checked>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div> --}}
					</div>

					<div class="form-group row">
						<p class="col-10 offset-2 text-warning">{{ trans('textos.cuenta_contable_manual_texto') }}</p>
						{{-- Numeración de la cuenta --}}
						<label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_numeracion')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="account" id="aaAccount" placeholder="{!! ucfirst(trans('textos.cuenta_numeracion')) !!}" autocomplete="off" maxlength="{{ $maxlength + 4 }}"/>
						</div>
					</div>
					<hr>

					<div class="form-group row">
						<p class="col-10 offset-2 text-warning">{{ trans('textos.cuenta_contable_pgc') }}</p>
						{{-- Level 1 --}}
						<label for="level1" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nivel')) !!} 1</label>
						<div class="col-lg-4">
							<x-select name="level1" class="select2 accountType" id="aaType1" data-level="1" :options="$types" label="tipo_selec"/>
						</div>

						{{-- Level 2 --}}
						<label for="level2" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nivel')) !!} 2</label>
						<div class="col-lg-4">
							<x-select name="level2" class="select2 accountType" id="aaType2" data-level="2" :options="[]" label="tipo_selec" disabled/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Level 3 --}}
						<label for="level3" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nivel')) !!} 3</label>
						<div class="col-lg-4">
							<x-select name="level3" class="select2 accountType" id="aaType3" data-level="3" :options="$types" label="tipo_selec" disabled/>
						</div>

						{{-- Level 4 --}}
						<label for="level4" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nivel')) !!} 4</label>
						<div class="col-lg-4">
							<x-select name="level4" class="select2 accountType" id="aaType4" data-level="4" :options="[]" label="tipo_selec" disabled/>
						</div>
					</div>	

					<div class="form-group row">
						{{-- Numeración restante --}}
						<p class="col-10 offset-2 text-warning">{{ trans('textos.cuenta_numeracion_completar') }}</p>
						<label for="digits" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.numeracion_completar')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="digits" id="aaDigits" autocomplete="off" maxlength="{{ $maxlength }}" disabled/>
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
$(document).ready(function () {
	//Validación cuenta creada manualmente:
	$('#aaAccount').on('input', function(){
		var account = $('#aaAccount').val();
		$('.accountType').val(null).trigger('change');
		$('#aaDigits').val('').prop('disabled', true);
		$('#aaAccount').val(account);
	});

	//Select anidados:
	$('.accountType').on('change', function(){
		var elem = $(this);
		var id = elem.attr('id');
		var level = id.substr(id.length - 1);
		var next = '#aaType'+(parseInt(level) + 1);
		var type = elem.val();

		//Si existe valor, se cancela la opción de la cuenta manual:
		if(type != ''){
			$('#aaAccount').val('');	
			$('#aaDigits').prop('disabled', false);
		}

		if(level == 1){
			$('#aaType2, #aaType3, #aaType4').empty().val('');	
		}else if(level == 2){
			$('#aaType3, #aaType4').empty().val('');
		}else if(level == 3){
			$('#aaType4').empty().val('');
		}
		
		if(type == null || type == ''){
			if(level == 1){
				$('#aaType2, #aaType3, #aaType4').attr('disabled', 'disabled');	
			}else if(level == 2){
				$('#aaType3, #aaType4').attr('disabled', 'disabled');
			}else if(level == 3){
				$('#aaType4').attr('disabled', 'disabled');
			}	
		}else{
			$.get("/admin/accounting-account-types/" + type + "/select", function (response, state) {
				if(response.length > 0){
					$(next).removeAttr('disabled');
					$(next).append('<option value="">' + txtTipoSelec + '</option>');
					for (i = 0; i < response.length; i++) {
						$(next).append('<option value="' + response[i].id + '">'+ response[i].code +' - '+ response[i].name + '</option>');
					}
				}
			});
		}
	});















	// //Habilitar / deshabilitar campo de numeración de la cuenta:
	// $('#chkInternalAccount').on('click', function(){
	// 	$('#aaAccount').val('');
	// 	if($(this).is(':checked')){
	// 		$('#search_company, #companyId').val('');
	// 		$('#aaAccount').prop('disabled', false);
	// 	}else{
	// 		$('#aaAccount').prop('disabled', true);
	// 	}
	// });

	// //Selector subtipo cuenta bancaria.
	// $('#aaType').on('change', function (event) {
    //     //Limpiamos posibles valores de provincia y población:
    //     $('#aaSubtype').empty().val('');
    //     $('#aaType option:selected').each(function () {
    //         var option = event.target.value;
    //        	var suboptions = subtypes[option];
    //        	console.log(suboptions);
    //     	if(typeof suboptions != 'undefined'){
    //             $('#aaSubtype').removeAttr('disabled');
    //             $("#aaSubtype").append('<option value="">' + txtSubtipoSelec + '</option>');
    //             for(var k in suboptions) {
    //                 $("#aaSubtype").append('<option value="' + k + '">' + suboptions[k] + '</option>');
    //             }
    //         }else{
    //         	$('#aaSubtype').attr('disabled', 'disabled');	
    //         }
    //     });
    // });
});
</script>
@endpush

@endsection