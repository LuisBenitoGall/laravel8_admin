@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="invoices">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Introducir factura de compra --}}
		<a href="{{ route('invoices.create-from-provider') }}" class="btn btn-info">
			<i class="fa fa-plus"></i>
			{!! ucfirst(trans('textos.factura_compra_introducir')) !!}
		</a>

		{{-- Control de facturación --}}
		<a href="{{ route('invoices.control') }}" class="btn btn-info">
			<i class="fa fa-tasks"></i>
			{!! ucfirst(trans('textos.facturacion_control')) !!}
		</a>
	</div>
</div>

{{-- Filtros --}}
<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('invoices.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.selec_cliente_proveedor_factura') !!}</p>
						</div>

						{{-- Selectores de cliente y proveedor --}}
						@include('admin.partials.selector-customer', ['all' => true, 'selected' => $customer_id])

						@include('admin.partials.selector-provider', ['all' => true, 'selected' => $provider_id])
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])
						{{-- Mes --}}
						@include('admin.partials.selector-month', ['selected_month' => $month])

						{{-- Grupos --}}
						<label for="group" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.grupo')) !!} </label>
						<div class="col-lg-2">
							<x-select name="group" class="select2" :options="$groups" value="{{ $group }}" label="grupo_selecciona"/>
						</div>
					</div>

					<div class="form-group row">
						<label for="invoice_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="invoice_id" maxlength="50">
						</div>

						<label for="order_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="order_id" maxlength="50">
						</div>

						<label for="po" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="po" maxlength="50">
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@if($customer || $provider)
	<input type="hidden" id="myCustomer" value="{{ $customer_id }}">
	<input type="hidden" id="myProvider" value="{{ $provider_id }}">
	<input type="hidden" id="myYear" value="{{ $year }}">
	<input type="hidden" id="myMonth" value="{{ $month }}">
	<input type="hidden" id="myGroup" value="{{ $group }}">

	@php
	$totalInvoicesAmount = 0;
	@endphp

	{{-- Tabla --}}
	<div class="row">
		<div class="col-12 mb-3">
			<h3>
				@if($customer_id == 'all')
					{!! trans('textos.facturas_ventas_relacion_general') !!}

				@elseif($provider_id == 'all')
					{!! trans('textos.facturas_compras_relacion_general') !!}

				@else
					@if($customer->id == session('currentCompany'))
						{!! ucfirst(trans('textos.facturas_compras_a')) !!} <b>{{ $provider->name ?? '' }}</b>
					@elseif($provider->id == session('currentCompany'))
						{!! ucfirst(trans('textos.facturas_ventas_a')) !!} <b>{{ $customer->name }}</b>
					@endif
				@endif
			</h3>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblInvoices">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="wdt150">
							@if($invoicesType == 'facturas_compras')
								{!! ucfirst(trans('textos.proveedor')) !!}
							@else
								{!! ucfirst(trans('textos.cliente')) !!}
							@endif
						</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.pedido')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.responsable')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</th>
						<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
						<th class="text-center wdt100">{!! trans('textos.importe_iva') !!}</th>
						<th class="text-center wdt100">{!! trans('textos.pago_metodo') !!}</th>
						<th class="wdt50">{!! trans('textos.mes') !!}</th>
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right"><b>{{ trans('textos.total') }}</b></td>
						<td class="text-right">{{ $totalInvoicesAmount }}{{ $currency->symbol }}</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

@else
	<div class="row">
		<div class="col-12 text-center pt-4">
			<h2>{!! trans('textos.filtra_facturas') !!}.</h2>
		</div>
	</div>
@endif

@push('scripts')
<script>
$(function(){
	//Tabla Invoices:
    if($('#tblInvoices').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val() != ''? $('#myCustomer').val():'0';
        var provider = $('#myProvider').val() != ''? $('#myProvider').val():'0'; 
        var year = $('#myYear').val();
        var month = $('#myMonth').val();
        var group = $('#myGroup').val();
        var orderId = $('#orderId').val();
        var poId = $('#poId').val();        //by purchase order.

        //Filters:

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblInvoices_'+window.location.pathname));

        $('#tblInvoices thead tr').clone(true).appendTo('#tblInvoices thead');
        $('#tblInvoices thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 11){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableInvoices.column(i).search() !== this.value){
                    tableInvoices
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        // var api_route = 'invoices/'+year+'/'+customer+'/'+provider;
        var api_route = `invoices/${year}/${customer}/${provider}/${month}/${group}`;

        if(orderId){
            api_route = 'invoices-by-order/'+orderId;
        }else if(poId){
            api_route = 'invoices-by-purchase/'+poId;
        }
        
        var tableInvoices = $('#tblInvoices').DataTable({
            order: [[ 5, "desc" ]],
            serverSide: false,
            processing: false,
            stateSave: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/"+api_route,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'invoice_num', name: 'invoices.invoice_num', className: 'text-right'},
                {data: 'company', name: 'companies.name'},
                {data: 'order', name: 'orders.order_num', className: 'text-right'},
                {data: 'manager', name: 'users.name'},
                {data: 'fiscal_date', className: 'text-right'},
                {data: 'expiration_date', className: 'text-right'},
                {data: 'amount', name: 'invoices.amount', className: 'text-right'},
                {data: 'amount_with_iva', name: 'invoices.amount_with_iva', className: 'text-right'},
                {data: 'payment_method', name: 'payment_methods.method'},
                {data: 'month'},
                //{data: 'workplace'},
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                // if(typeof year != 'undefined'){
                    var api = this.api(), data;
                    var json = api.ajax.json();

                    var currency = false;
                    var refund = false;
                    api.rows().every(function(){
                        var data = this.data();
                    });

                    if(!currency) currency = '€';
     
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i){
                        //console.log(i);
                        return typeof i === 'string'? 
                        i.replace(/[\'+currency+',]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                    };

                    //Total:
                    total = api
                    .column(6, { search: 'applied' })
                    .data()
                    .reduce(function(a, b){
                        return intVal(a) + intVal(b);
                    }, 0);

                    $(api.column(6).footer()).html(
                        total.toFixed(2)+currency   
                    );

                    //Total con IVA:
                    total_iva = api
                    .column(7, { search: 'applied' })
                    .data()
                    .reduce(function(a, b){
                        return intVal(a) + intVal(b);
                    }, 0);

                    $(api.column(7).footer()).html(
                        total_iva.toFixed(2)+currency   
                    );
     
                    //Total por filtro aplicado:
                    // total_amount = api
                    // .column(5, { search: 'applied' })
                    // .data()
                    // .reduce(function(a, b){
                    //     return intVal(a) + intVal(b);
                    // }, 0);

                    // $(api.column(5).footer()).html(
                    //     total_amount.toFixed(2)+currency    
                    // );
                //}
            }
        });
    }
	
	//Toggle selector clientes o proveedores:
	 $('#customerId').on('change', function(e){
         $('#providerId').val(['']).trigger('change.select2');
	 });
	 $('#providerId').on('change', function(e){
         $('select#customerId').val(['']).trigger('change.select2');
	 });
});
</script>
@endpush

@endsection
