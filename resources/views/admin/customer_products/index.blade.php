@extends('admin.partials.layout')

@section('content')

@if($customer)
	<input type="hidden" id="dtCustomer" value="{{ $customer->id }}">
	<input type="hidden" id="dtBusinessArea" value="@if($business_area) {{ $business_area->id }} @endif">
@elseif($provider)
	<input type="hidden" id="dtProvider" value="{{ $provider->id }}">
@endif

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Retorno a clientes o proveedores --}}
		@if($customer)
			<a href="{{ route('customers.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_clientes') !!}
			</a>
		@elseif($provider)
			<a href="{{ route('providers.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proveedores') !!}
			</a>
		@endif

		{{-- Nuevo producto - condiciones
			1. Disponer de permisos de creación de producto.	
			2. Exclusivo para proveedores tutelados.
			...
		--}}
		@can('products.create')
			@if(isset($account) && $account->guardian == session('currentCompany'))
	    		<a href="{{ route('products.create', $provider->id) }}" class="btn btn-info">
	    			<i class="fa fa-plus"></i>
	    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
	    		</a>
	    	@endif
        @endcan
	</div>
</div>

{{-- Filtro: --}}
@if(count($areas) > 0)
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
			@if($customer)
				{!! trans('textos.catalogo_para') !!} 
				<b>{{ $customer->name }}</b>
			@elseif($provider)
				{!! trans('textos.catalogo_de') !!}
				<b>{{ $provider->name }}</b>
			@endif

			@if($business_area)
				- {{ $business_area->name }}
			@endif
		</h3>
	</div>

	{{-- Vista listado --}}
	@php
	$tbl = $customer? 'tblCustomerProducts':'tblProviderProducts';
	@endphp

	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="{{ $tbl }}">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					@if($tbl == 'tblProviderProducts')
						<th>{!! ucfirst(trans('textos.ref_interna')) !!}</th>
					@endif
					<th class="text-center wdt200">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.precio_catalogo')) !!}</th>
					@if($tbl == 'tblCustomerProducts')
						<th class="text-center wdt150">{!! ucfirst(trans('textos.precio_cliente')) !!}</th>
					@endif
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Modal de correspondencia de referencia --}}
{{-- @include('admin.customer_products.partials.modal-set-reference') --}}

@push('scripts')
<script>
$(function(){
	//Tabla Customer Products:
    if($('#tblCustomerProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#dtCustomer').val();
        var ba = $('#dtBusinessArea').val();

        //Filters:
        $('#tblCustomerProducts thead tr').clone(true).appendTo('#tblCustomerProducts thead');
        $('#tblCustomerProducts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 2 || i == 4 || i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCustomerProducts.column(i).search() !== this.value){
                    tableCustomerProducts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableCustomerProducts = $('#tblCustomerProducts').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/customer-products/"+customer+"/"+ba,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'products.name'},
                {data: 'descripcion', name:"products.description"},
                {data: 'image', className: 'text-center', orderable: false, searchable: false},
                {data: 'price', name: 'products.price', className: 'text-right'},
                {data: 'customer_price', className: 'text-right', orderable: false, searchable: false},
                {data: 'acciones', className: 'text-right', orderable: false, searchable: false}
            ]
        });
    }

    //Tabla Provider Products:
    if($('#tblProviderProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var provider = $('#dtProvider').val();

        //Filters:
        $('#tblProviderProducts thead tr').clone(true).appendTo('#tblProviderProducts thead');
        $('#tblProviderProducts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3 || i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableProviderProducts.column(i).search() !== this.value){
                    tableProviderProducts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProviderProducts = $('#tblProviderProducts').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/provider-products/"+provider,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'descripcion'},
                {data: 'internal_ref'},
                {data: 'image', className: 'text-center', orderable: false, searchable: false},
                {data: 'price', className: 'text-right'},
                {data: 'acciones', className: 'text-right', orderable: false, searchable: false}
            ]
        });
    }

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

    // Modal correspondencia referencia de proveedor.
    $(document).on('click', '.btnProviderProduct', function(){
    	var provider_product = $(this).data('id');	
    	$('#providerProductId').val(provider_product);
    });
});
</script>
@endpush

@endsection