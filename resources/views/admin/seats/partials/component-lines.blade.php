<hr>
<div class="row">
	<div class="col-12 col-md-6 offset-md-2">
		<h3 class="my-3">{{ $title }}</h3>
	</div>	
</div>

<div class="form-group row">
	{{-- Cuenta contable --}}
	<label for="accounting_account_" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}</label>
	<div class="col-lg-4 position-relative">
		<x-input type="text" name="accounting_account_" class="search_accounting_account" datas="id:1" id="searchAccountingAccount1" placeholder="{!! ucfirst(trans('textos.cuenta_contable')) !!}" maxlength="150"/>
	</div>

	<input type="hidden" name="accounting_account_id_" id="accountingAccountId1">

	{{-- Tipo de línea --}}
	<label for="type_" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.tipo')) }}</label>
	<div class="col-lg-2">
		<x-select name="type_" class="select2" label="tipo_selec" :options="$seat_lines_types"/>
	</div>
</div>

<div class="form-group row">
	{{-- Debe --}}
	<label for="debe_" class="col-lg-2 col-form-label">{{ trans('textos.debe') }}</label>
	<div class="col-lg-2">
		<x-input type="text" name="debit_" class="text-right set-decimals" autocomplete="off" maxlength="20" group="right" groupvalue="{{ $currency->symbol }}"/>
	</div>

	{{-- Haber --}}
	<label for="haber_" class="col-lg-2 col-form-label">{{ trans('textos.haber') }}</label>
	<div class="col-lg-2">
		<x-input type="text" name="credit_" class="text-right set-decimals" autocomplete="off" maxlength="20" group="right" groupvalue="{{ $currency->symbol }}"/>
	</div>	

	{{-- Añadir --}}
	<div class="col-lg-1" style="padding-top: 3px;">
		<button class="btn btn-warning btn-circle btnAddLine" rel="tooltip" title="Añadir línea">
			<i class="fa fa-plus"></i>
		</button>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Cálculo importes:
	$('.calc_amount, .calc_iva').on('change', function(){
		var elem = $(this);
		var father = elem.closest('.form-group');
		var amount = father.find('.calc_amount').val();
		var iva = father.find('.calc_iva').val();

		iva_amount = (amount > 0 && iva > 0)? (amount * iva) / 100:'0';
		total_amount = parseFloat(amount) + parseFloat(iva_amount);

		ta = isNaN(total_amount)? '':total_amount.toFixed(2);
		father.find('.amount-here').val(ta);
	});

	//Añadir líneas:
	var i = $('.line-num').length > 0? $('.line-num').length + 1:1;
	$('.btnAddLine').on('click', function(e){
		e.preventDefault();
		var accounting_account_id = $('input[name="accounting_account_id_"]').val();
		var accounting_account = $('input[name="accounting_account_"]').val();
		var type_id = $('select[name="type_"]').val();
		var type = type_id != null? $('select[name="type_"] option:selected').text():'';
		var debe = $('input[name="debit_"]').val();
		var haber = $('input[name="credit_"]').val();
		$('.line-titles').css("visibility", "visible");

		var html = '<div class="form-group row">';
			html += '<div class="col-1 text-right line-num" style="padding-top: 7px;">'+i+'.</div>';
			html += '<div class="col-md-4"><input type="text" class="form-control" value="'+accounting_account+'" readonly></div>';
			html += '<input type="hidden" name="accounting_account_id[]" value="'+accounting_account_id+'">';
			html += '<div class="col-md-1"><input type="text" class="form-control" value="'+type+'" readonly></div>';
			html += '<input type="hidden" name="type[]" value="'+type+'">';
			html += '<div class="col-md-2"><input type="text" name="debit[]" class="form-control text-right from-debe" value="'+debe+'" readonly></div>';
			html += '<div class="col-md-2"><input type="text" name="credit[]" class="form-control text-right from-haber" value="'+haber+'" readonly></div>';
			html += '<div class="col-1 pt-2"><button class="btn btn-danger btn-circle btn-circle-mini btnRemoveLine"><i class="fa fa-times"></i></button></div>';
		html += '</div>';

		$('#seatLines').append(html);
		i++;

		debe = debe != ''? debe:'0';
		var debe_ = $('#totalDebe').text();
		debe_ = debe_ != ''? debe_:'0'; 
		debe_ = parseFloat(debe_) + parseFloat(debe);
		$('#totalDebe').text(debe_);

		haber = haber != ''? haber:'0';
		var haber_ = $('#totalHaber').text();
		haber_ = haber_ != ''? haber_:'0';
		haber_ = parseFloat(haber_) + parseFloat(haber);
		$('#totalHaber').text(haber_);

		var diff = debe_ - haber_;
		$('#totalDiff').text(diff);

		$('input[name="accounting_account_id_"]').val('');
		$('input[name="accounting_account_"]').val('');
		$('select[name="type_"]').val('').trigger('change');
		$('input[name="debit_"]').val('');
		$('input[name="credit_"]').val('');
	});

	//Modificar importes:
	$(document).on('change', '.lineAmount', function(){
		var amount = $(this).val();
		var debe_ = $('#totalDebe').text();
		var haber_ = $('#totalHaber').text();
		
		if($(this).hasClass('debe__')){
			debe_ = parseFloat(debe_) + parseFloat(amount);
		}
		if($(this).hasClass('haber__')){
			debe_ = parseFloat(haber_) + parseFloat(amount);
		}
		$('#totalDebe').text(debe_);
		$('#debeTotal').val(debe_);
		$('#totalHaber').text(haber_);
		$('#haberTotal').val(haber_);
		var diff = debe_ - haber_;
		$('#totalDiff').text(diff);
	});

	//Eliminar línea:
	$(document).on('click', '.btnRemoveLine', function(e){
		e.preventDefault();
		$(this).closest('.form-group').remove();
		var j = 1;
		$('.line-num').each(function(index){
			$(this).text(j+'.');
			j++;
		});
		i--;	//Restamos 1 a i para mantener la correlación de números.

		//Recalculando totales:
		var debe_amount = 0;
		$('.from-debe').each(function(index){
			if($(this).val() > 0){
				debe_amount = parseFloat(debe_amount) + parseFloat($(this).val());
			}
		});
		var haber_amount = 0;
		$('.from-haber').each(function(index){
			if($(this).val() > 0){
				haber_amount = parseFloat(haber_amount) + parseFloat($(this).val());
			}
		});

		var diff = debe_amount - haber_amount;

		$('#totalDebe').text(debe_amount);
		$('#totalHaber').text(haber_amount);
		$('#totalDiff').text(diff);
	});

	//Validando importes de líneas:
	$('#sbtSeat').on('click', function(e){
		var debe_amount = $('#debeTotal').val();
		$('.from-debe').each(function(index){
			if($(this).val() > 0){
				debe_amount = parseFloat(debe_amount) + parseFloat($(this).val());
			}
		});
		var haber_amount = $('#haberTotal').val();
		$('.from-haber').each(function(index){
			if($(this).val() > 0){
				haber_amount = parseFloat(haber_amount) + parseFloat($(this).val());
			}
		});

		if(debe_amount != haber_amount){
        	var msg = '{!! trans('textos.asiento_contable_incorrecto') !!}';

        	swal.fire({
                title: msg,
                text: "",
                icon: 'warning',
                iconColor: color_warning,
                showCancelButton: false,
                confirmButtonColor: color_danger,
                cancelButtonColor: color_danger,
                confirmButtonText: txtCancelar,
                cancelButtonText: txtCancelar
            });
        	e.preventDefault();

		}else{
			return true;
		}
	});
});
</script>
@endpush