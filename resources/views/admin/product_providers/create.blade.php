@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('providers.edit')
			<a href="{{ route('product-providers.index', $product) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proveedores') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{{ trans('textos.proveedor_correspondencia_producto') }} <u>{{ $product->name }}</u>
		</h3>
	</div>

	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Condiciones necesarias --}}
				{{-- 1. Activada gestión de proveedores: --}}
				@if(!$providers)
					<div class="row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.proveedores_gestion_off')) !!}</b> 
							<a href="{{ route('company-settings.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.configuracion')) !!}</a>
						</label>
					</div>	

				{{-- 2. Debe haber proveedores --}}
				@elseif($providers && !$providers->count())
					<div class="form-group row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.proveedores_no_hay')) !!}</b> 
							<a href="{{ route('providers.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.proveedores')) !!}</a>
						</label>
					</div>	

				{{-- 3. Se cumplen las condiciones --}}
				@else
					<form method="post" autocomplete="off" action="{{ route('product-providers.set-provider', $product) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}

						{{-- Selecciona proveedor --}}
						<div class="form-group row">
							<label for="provider_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!} *</label>
							<div class="col-lg-6">
								@php
								$providers_ = [];

								$prov = $provider? $provider->id:'';
								@endphp

								@foreach($providers as $pvd)
									@php
									$company = $pvd->tradename? $pvd->name.' ('.$pvd->tradename.')':$pvd->name;
									$providers_[$pvd->id] = $company;
									@endphp
								@endforeach

								<x-select name="provider_id" class="select2" label="selec_proveedor" :options="$providers_" value="{{ $prov }}" required/>
							</div>

							<x-btn-submit name="submit_save" classdiv="col-sm-10 text-right" label="{{ trans('textos.continuar') }}" inline="true"/>
						</div>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>

{{-- Proveedor declarado --}}
@if($provider)
	<input type="hidden" id="productId" value="{{ $product->id }}">
	<input type="hidden" id="providerId" value="{{ $provider->id }}">

	{{-- Productos fuera de catálogo del proveedor --}}
	@if($products_out_catalogue->count()){
		<div class="row">
			<div class="col-md-11 mb-3">
				<h3>
					{{ ucfirst(trans('textos.productos_fuera_catalogo')) }}
					<u>{{ $provider->name }}</u>
				</h3>
			</div>	

			<div class="table-responsive">
				<table class="table table-hover dTable" id="tblProductProvidersOutCatalogue">
					<thead>
						<tr>
							<th>{!! ucfirst(trans('textos.producto')) !!}</th>
							<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
						</tr>
					</thead>

					<tbody>
						@foreach($products_out_catalogue as $poc)
							<tr>
								<td></td>
								<td></td>
								<td class="text-right"></td>
								<td class="text-right"></td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>	
	@endif

	<div class="row">
		<div class="col-md-11 mb-3">
			<h3>
				{{ ucfirst(trans('textos.catalogo')) }}
				<u>{{ $provider->name }}</u>
			</h3>
		</div>	

		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblProductProvidersCorrespondences">
				<thead>
					<tr>
						<th>{!! ucfirst(trans('textos.producto')) !!}</th>
						<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.imagen')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
@endif

@push('scripts')
<script>
$(function(){
	//Setear correspondencia de referencia de proveedor.
	$(document).on('change', '.chkSetRef', function(e){
		e.preventDefault();
		var elem = $(this);
		var customer_product_id = $('#productId').val();
		var provider_product_id = $(this).val();
		var provider_id = $('#providerId').val();
		if($(this).is(':checked')){
            var status = 1;
        }else{
            var status = 0;
        }  

		$.ajax({
			headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                _token:$('meta[name="csrf-token"]').attr('content'),
                customer_product_id:customer_product_id,
                provider_id:provider_id,
                provider_product_id:provider_product_id,
                status:status
            },
            url: "/admin/product-providers/store",
            type: "POST",
            cache: false,
            datatype: "JSON",
            success: function(response){
            	if(response){
	                $('.check-ko').remove();
	                elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
	                $('.check-ok').delay(1000).fadeOut();
	            }else{
	                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
	            }
    		}
        });
	});
});
</script>
@endpush

@endsection		