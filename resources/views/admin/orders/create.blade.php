@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de clientes --}}
		@if($company_id)
			<a href="{{ route('customers.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_clientes') !!}
			</a>	
		@endif

		{{-- Listado de pedidos --}}
		@can('orders.index')
			<a href="{{ route('orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_pedidos') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Condiciones necesarias --}}
				{{-- 1. Activada gestión de clientes o proveedores: --}}
				@if(!$companies)
					<div class="row">
						<label class="col-12 text-center text-warning mt-4">
							<b>
								@if($type == 'sale')
									{!! ucfirst(trans('textos.clientes_gestion_off')) !!}
								@else
									{!! ucfirst(trans('textos.proveedores_gestion_off')) !!}
								@endif
							</b> 
							<a href="{{ route('company-settings.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.configuracion')) !!}</a>
						</label>
					</div>	

				{{-- 2. Activo el módulo de productos, sólo para ventas --}}
				@elseif(!$module_products && $type == 'sale')
					<div class="form-group row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.modulo_catalogo_activar')) !!}</b> 
							<a href="{{ route('company-modules.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.modulo_activar')) !!}</a>
						</label>
					</div>	

				{{-- 3. Debe haber clientes o proveedores --}}
				@elseif($companies && $companies->count() == 0)
					<div class="form-group row">
						<label class="col-12 text-center text-warning mt-4">
							@if($type == 'sale')
								<b>{!! ucfirst(trans('textos.clientes_no_hay')) !!}</b> 
								<a href="{{ route('customers.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.clientes')) !!}</a>
							@else
								<b>{!! ucfirst(trans('textos.proveedores_no_hay')) !!}</b> 
								<a href="{{ route('providers.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.proveedores')) !!}</a>
							@endif
						</label>
					</div>	

				{{-- 4. Se cumplen las condiciones --}}
				@else
					<form method="post" autocomplete="off" action="{{ route('orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}
						<input type="hidden" name="type" value="{{ $type }}">
							
						<div class="form-group row">
							{{-- Selecciona cliente o proveedor --}}
							<label for="company_id" class="col-lg-2 col-form-label">@if($type == 'sale') {!! ucfirst(trans('textos.cliente')) !!} @else {!! ucfirst(trans('textos.proveedor')) !!} @endif *</label>
							<div class="col-lg-5">
								@php
								$companies_ = [];
								@endphp

								@foreach($companies as $co)
									@php
									$companies_[$co->id] = $co->name.' ('.$co->tradename.')';
									@endphp
								@endforeach

								@php
								$label = $type == 'sale'? 'selec_cliente':'selec_proveedor';
								@endphp

								<x-select name="company_id" id="formCustomerId" class="select2" label="{{ $label }}" :options="$companies_" value="{{ $company_id }}" required/>

								{{-- Error tipo pedido --}}
								@error('type')
							        <span class="invalid-feedback" role="alert">
							            <strong>{{ $message }}</strong>
							        </span>
							    @enderror
							</div>

							{{-- Generar proyecto --}}
							@if($type == 'sale')
								<label for="set_project" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyecto_generar')) !!}</label>
								<div class="col-lg-1 pt-1">
							        <label class="custom-chk-rdb">
							        	<input type="checkbox" name="set_project" @if(session('companySettings')->project_required) checked @endif>
							        	<span class="chk"></span>
							    	</label>

							    	<div class="position-absolute" style="left: 65px; top: 0;">
					            		{!! App\Helpers\helpers::info('order-set-project') !!}
					            	</div>
							    </div>	
							@endif
						</div>

						{{-- Selecciona usuarios del proveedor para compras --}}
						@if($type == 'purchase')
							<div class="form-group row">
								<label for="receivers" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.notificar_a')) !!}</label>
								<div class="col-lg-8">
									<x-select name="receivers[]" id="purchaseReceivers" class="select2" multiple :options="[]" info="provider-users-notificate"/>
								</div>
							</div>	
						@endif

						<x-btn-submit name="submit_save" classdiv="col-sm-10 text-right" label="{{ trans('textos.continuar') }}"/>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	var tipo = '{{ $type }}';
	if(tipo == 'purchase'){
		//Obtener destinatarios de notificación:
		//24/09/2022: se modifica la consulta para obtener los usuarios vinculados al proveedor, no sólo los usuarios.
		$('#formCustomerId').on('change', function(event){
			var company = event.target.value;
			$('#purchaseReceivers').empty();
			//$.get("/admin/employees/"+company+"/company-employees/true", function(response, state){
			$.get("/admin/user-companies/"+company+"/show", function(response, state){
				if(response){
					for(i=0; i<response.length; i++){
						$('#purchaseReceivers').append('<option value="'+response[i].id+'">'+response[i].name+' '+response[i].surname+'</option>');		
					}
				}
			});
		});
	}
});
</script>
@endpush

@endsection