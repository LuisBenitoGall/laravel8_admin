<div class="form-group row">
	<label for="iban" class="col-lg-2">{!! ucfirst(trans('textos.iban')) !!}*</label>	
	<label for="entity" class="col-lg-2">{!! ucfirst(trans('textos.entidad')) !!}*</label>	
	<label for="office" class="col-lg-2">{!! ucfirst(trans('textos.oficina')) !!}*</label>
	<label for="dc" class="col-lg-2">{!! ucfirst(trans('textos.digitos_control')) !!}*</label>		
	<label for="digits" class="col-lg-4">{!! ucfirst(trans('textos.cuenta')) !!}*</label>	

	@php
	if(isset($account) && $account){
		$iban = $account->iban;
		$entity = $account->entity;
		$office = $account->office;
		$dc = $account->dc;
		$digits = $account->digits;
	}else{
		$iban = '';
		$entity = '';
		$office = '';
		$dc = '';
		$digits = '';
	}
	@endphp

	<div class="col-lg-2">
		<x-input type="text" name="iban" class="text-right" autocomplete="off" maxlength="4" placeholder="- - - -" value="{{ $iban }}" required/>
    </div>
    <div class="col-lg-2">
		<x-input type="text" name="entity" class="text-right" autocomplete="off" maxlength="4" placeholder="- - - -" value="{{ $entity }}" required/>
    </div>
    <div class="col-lg-2">
		<x-input type="text" name="office" class="text-right" autocomplete="off" maxlength="4" placeholder="- - - -" value="{{ $office }}" required/>
    </div>
    <div class="col-lg-2">
		<x-input type="text" name="dc" class="text-right" autocomplete="off" maxlength="2" placeholder="- -" value="{{ $dc }}" required/>
    </div>
    <div class="col-lg-4">
		<x-input type="text" name="digits" class="text-right" autocomplete="off" maxlength="10" placeholder="- - - - - - - - - -" value="{{ $digits }}" required/>
    </div>
</div>