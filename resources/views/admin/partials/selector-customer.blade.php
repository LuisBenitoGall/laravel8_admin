@if($customers && $customers->count())
	<label for="customer_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!} @if(isset($required) && $required)*@endif</label>
	<div class="col-lg-4">
		@php
		$customers_ = [];

		if($all){
			$customers_[''] = ucfirst(trans('textos.ninguno'));
			$customers_['all'] = trans('textos.clientes_todos');	
		}
		
		foreach($customers as $ctm){
			$customers_[$ctm->id] = $ctm->name;
		}

		$customerId = $customer? $customer->id:$selected;

		$required_ = isset($required) && $required? true:false;
		@endphp

		<x-select name="customer_id" class="select2" label="cliente_selec" :options="$customers_" value="{{ $customerId }}" required="{{ $required_ }}"/>
	</div>

@else
	<input type="hidden" name="customer_id" value="0">	
@endif
