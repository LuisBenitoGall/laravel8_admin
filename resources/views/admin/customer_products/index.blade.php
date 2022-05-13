@extends('admin.partials.layout')

@section('content')

@if($customer)
	<input type="hidden" id="dtCustomer" value="{{ $customer->id }}">
	<input type="hidden" id="dtBusinessArea" value="@if($business_area) {{ $business_area->id }} @endif">
@endif

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('customers.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_clientes') !!}
		</a>
	</div>
</div>

{{-- Filtro: --}}
@if($areas->count() > 0)
	<div class="row">
		<div class="col-12 mb-3">						
			<div class="card mb-3">
				<div class="card-body">
					<form method="post" autocomplete="off" action="{{ route('customer-products.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}	
						<input type="hidden" name="customer_id" value="{{ $customer->id }}">

						<div class="form-group row">
							<p class="col-10 offset-lg-2 text-warning">{{ trans('textos.catalogo_cliente_filtro_texto') }}</p>

							<label for="ba_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.area_negocio')) !!} </label>
							<div class="col-lg-4">
								<select name="ba_id" class="form-control select2">
									<option value="">{{ trans('textos.todos') }}</option>
									@foreach($areas as $ar)
										@php
										$selected = $business_area && $business_area->id == $ar->id? 'selected':'';
										@endphp
										<option value="{{ $ar->id }}" {{ $selected }}>{{ $ar->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="col-lg-1 text-right">
					  			<input type="submit" name="submit_search" class="btn btn-info" value="{!! ucfirst(trans('textos.buscar')) !!}">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endif

<div class="row">
	<div class="col-12 mb-3">
		<h3>
			{!! trans('textos.catalogo_para') !!} 
			<b>{{ $customer->name }}</b>
			@if($business_area)
				- {{ $business_area->name }}
			@endif
		</h3>
	</div>

	{{-- Vista listado --}}
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblCustomerProducts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center wdt200">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th class="text-center wdt200">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	// Status de producto en catálogo para cliente.
    $(document).on('click', '.btnCustomerProductStatus', function(){
        var status = '';
        var elem = $(this);
        var padre = elem.closest('tr');
        var customer_id = $('#dtCustomer').val();
        var ba_id = $('#dtBusinessArea').val();
        //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
        if(elem.siblings('input').is(':checked')){
            status = 0;
            padre.find('input[type=text]').prop('readonly',true);
        }else{
            status = 1;
            padre.find('input[type=text]').prop('readonly',false);
        }  
        var id = $(this).data('product');
        var price = $(this).data('price');
        $.get("/admin/customer-products/"+customer_id+"/status/"+id+"/"+status+"/"+price+"/"+ba_id+"", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        }); 
    });

    // Precio de producto en catálogo para cliente.
    $(document).on('change', '.customerProductPrice', function(){
        var status = '';
        var elem = $(this);
        var customer_id = $('#dtCustomer').val();
        var ba_id = $('#dtBusinessArea').val();
        var id = $(this).data('product');
        var price = $(this).val();
        $.get("/admin/customer-products/"+customer_id+"/update-price/"+id+"/"+price+"/"+ba_id+"", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.parent('div').prepend('<i class="fa fa-check check-ok absolute"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.parent('div').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        }); 
    });
});
</script>
@endpush

@endsection