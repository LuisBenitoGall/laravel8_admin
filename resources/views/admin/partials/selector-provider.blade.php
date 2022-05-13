@if($providers && $providers->count())
	<label for="provider_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!} </label>
	<div class="col-lg-4">
		@php
		$providers_[''] = ucfirst(trans('textos.ninguno'));
		$providers_['all'] = trans('textos.proveedores_todos');
		foreach($providers as $ctm){
			$providers_[$ctm->id] = $ctm->name;
		}

		$providerId = $provider? $provider->id:$selected;
		@endphp

		<x-select name="provider_id" class="select2" label="proveedor_selec" :options="$providers_" value="{{ $providerId }}"/>
	</div>

@else
	<input type="hidden" name="provider_id" value="0">
@endif