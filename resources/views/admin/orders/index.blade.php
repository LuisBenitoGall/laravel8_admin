@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="orders">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Panel ventas: --}}
		<a href="{{ route('orders.control-panel-sales') }}" class="btn btn-info">
			<i class="fa fa-chalkboard"></i>
			{!! ucfirst(trans('textos.panel_control_ventas')) !!}
		</a>

		{{-- Panel compras: --}}
		<a href="{{ route('orders.control-panel-purchases') }}" class="btn btn-info">
			<i class="fa fa-chalkboard"></i>
			{!! ucfirst(trans('textos.panel_control_compras')) !!}
		</a>

		{{-- Panel producción --}}
		@if(session('companySettings')->production_previsions)
			<a href="{{ route('order-production-prevision.control-panel') }}" class="btn btn-info">
				<i class="fa fa-chalkboard"></i>
				{!! ucfirst(trans('textos.produccion_panel_control')) !!}
			</a>
		@endif

		{{-- Nueva venta --}}
		@if(session('companySettings') && session('companySettings')['customers_management'])
			@can('orders.create')
				<a href="{{ route('orders.create', 'sale') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.venta_nueva')) !!}
				</a>
			@endcan
		@endif

		{{-- Nueva compra --}}
		@if(session('companySettings') && session('companySettings')['providers_management'])
			@can('orders.create')
				<a href="{{ route('orders.create', 'purchase') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.compra_nueva')) !!}
				</a>
			@endcan
		@endif

		{{-- Informes - condiciones: 
			1. Exclusivo proveedor.
			2. Viene declarado el cliente.
		--}}
		@if($customer_id && $provider && $provider->id == session('currentCompany'))
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-set-report">
				<i class="fa fa-chart-bar"></i>
				{!! ucfirst(trans('textos.informes')) !!}
			</a>
		@endif
	</div>
</div>

{{-- Formulario filtro --}}
@include('admin.orders.partials.form-filter')

@if($customer_id || $provider_id)
	<input type="hidden" id="myCustomer" value="{{ $customer_id }}">
	<input type="hidden" id="myProvider" value="{{ $provider_id }}">
	<input type="hidden" id="myOwner" value="@if($owner){{ $owner }}@else{{ 0 }}@endif">
	<input type="hidden" id="myYear" value="{{ $year }}">
	<input type="hidden" id="myStatus" value="@if($status){{ $status }}@else{{ 0 }}@endif">
	<input type="hidden" id="myModuleAccounting" value="{{ $module_accounting }}">
	<input type="hidden" id="myOrderBuy" value="{{ $order_buy }}">
	<input type="hidden" id="myFrom" value="{{ $from }}">
	<input type="hidden" id="myTo" value="{{ $to }}">
	<input type="hidden" id="myGroups" value="{{ $groups_ }}">

	{{-- Tabla --}}
	<div class="row">
		<div class="col-12 mb-3">
			<h3>
				@if($customer_id == 'all')
					{!! trans('textos.ventas_relacion_general') !!}

				@elseif($provider_id == 'all')
					{!! trans('textos.compras_relacion_general') !!}

				@else
					@if($customer->id == session('currentCompany'))
						{!! ucfirst(trans('textos.compras_a')) !!} <b>{{ $provider->name }}</b>
					@elseif($provider->id == session('currentCompany'))
						{!! ucfirst(trans('textos.ventas_a')) !!} <b>{{ $customer->name }}</b>
					@endif
				@endif
			</h3>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblOrders">
				<thead>
					<tr>
						<th class="d-none">&nbsp;</th>
						<th class="text-center wdt50">#</th>
						{{-- <th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th> --}}
						<th class="wdt100">{!! ucfirst(trans('textos.responsable')) !!}</th>
						<th class="d-none">&nbsp;</th>
						<th class="wdt150">
							@if($ordersType == 'compras')
								{!! ucfirst(trans('textos.proveedor')) !!}
							@else
								{!! ucfirst(trans('textos.cliente')) !!}
							@endif
						</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.agrupacion')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
						<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
						<!-- <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th> -->
						<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
						{{-- <th>{!! trans('textos.area_negocio') !!}</th> --}}
						{{-- <th class="wdt100">{!! trans('textos.area') !!}</th> --}}
						<th class="wdt100">{!! trans('textos.centro_coste') !!}</th>
						<th class="wdt50">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td class="d-none"></td>
						<td></td>
						<td></td>
						<td class="d-none"></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right"><b>{{ trans('textos.total') }}</b></td>
						<td class="text-right" id="ordersAmount"></td>
						<td colspan="4"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

@else
	<div class="row">
		<div class="col-12 text-center pt-4">
			<h2>{!! trans('textos.filtra_pedidos') !!}.</h2>
		</div>
	</div>
@endif

{{-- Modals: --}}
{{-- Factura --}}
{{-- @include('admin.orders.partials.modal-generate-invoice') --}}

{{-- Set report --}}
@include('admin.orders.partials.modal-set-report')

@push('scripts')
<script>
$(function(){
    //Tabla Orders:
	if($('#tblOrders').length > 0){
	    var tokenDT = $('meta[name="csrf-token"]').attr('content');
	    var customer = $('#myCustomer').val() != ''? $('#myCustomer').val():false;
	    var provider = $('#myProvider').val() != ''? $('#myProvider').val():false;
	    var owner = $('#myOwner').val() && $('#myOwner').val() != ''? $('#myOwner').val():false;
	    var year = $('#myYear').val();
	    var status = $('#myStatus').val() && $('#myStatus').val() != ''? $('#myStatus').val():false;
	    var from = $('#myFrom').val() && $('#myFrom').val() != ''? $('#myFrom').val():'0';
	    var to = $('#myTo').val() && $('#myTo').val() != ''? $('#myTo').val():'0';
	    var groups = $('#myGroups').val() != ''? $('#myGroups').val():false;
	    var accounting_on = $('#myModuleAccounting').val() == 1? true:false;
	    var recordsTotal;       //Variable número de registros

	    var orderbuy = $('#myOrderBuy').val() && $('#myOrderBuy').val() != '' ? $('#myOrderBuy').val():false;

	    // Filters:

	    // Load data from localstorage
	    var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrders_'+window.location.pathname));

	    $('#tblOrders thead tr').clone(true).appendTo('#tblOrders thead');
	    $('#tblOrders thead tr:eq(1) th').each(function(i){
	        var title = $(this).text(); 
	        if(i == 13){
	            $(this).html('');
	        }else{
	            // Check field on json of localstorage
	            let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
	            let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
	            $(this).html(htmlRaw);
	            let element = $(this).html(htmlRaw);

	            //Búsqueda por nombre y apellidos.
	            if (i == 2) {
	                $('input', element).on('keyup change', function(){
	                    // Diferenciamos si es nombre o apellido
	                    let fullname = this.value.split(' ');

	                    // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
	                    // caso contrario se busca en ambos campos
	                    if(fullname.length == 1) {
	                        // La busqueda se hace en el campo nombre
	                        console.log(`El nombre es ${fullname[0]}`);
	                        tableOrders
	                            .column(2)
	                            .search(fullname[0])
	                            .column(3)
	                            .search('')
	                            .draw();
	                    }else{
	                        // La busqueda se hace en ambos campos (nombre y apellido)
	                        console.log(`El nombre es ${fullname[0]} y el apellido es ${fullname[1]}`);
	                        tableOrders
	                            .column(2)
	                            .search(fullname[0])
	                            .column(3)
	                            .search(fullname[1])
	                            .draw();
	                    }
	                });
	            }
	            // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
	            // aquí el código retirado
	        }
	        $('input', this).on('keyup change', function(){
	            // Ignoramos la busqueda del campo de nombres
	            if (i == 2) {
	                return;
	            }

	            if(tableOrders.column(i).search() !== this.value){
	                tableOrders
	                    .column(i)
	                    .search(this.value)
	                    .draw();
	            }
	        });
	    });
	    
	    var tableOrders = $('#tblOrders').DataTable({
	        order: [[ 0, "desc" ]], // Ordenar siempre por esta sección, no hacerlo desde el controlador.
	        serverSide: true,
	        processing: true,
	        stateSave: true,
	        // drawCallback: function(){
	        //     var api = this.api(), data;
	        //     var json = api.ajax.json();
	        //     var currency = '€';

	        //     var intVal = function (i) {
	        //         return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
	        //     };

	        //     // Suma total todos los registros:
	        //     var total = api
	        //     .column(8)
	        //     .data()
	        //     .reduce(function (a, b) {
	        //         return intVal(a) + intVal(b);
	        //     }, 0);



	        //     // var total = api.column(8, {search: 'applied'}).data().sum();
	        //     $(api.column(8).footer()).html(
	        //         total.toFixed(2)+currency
	        //     );
	        //     //     $('.select2-dt').select2();
	        // },
	        ajax: {
	            "type": "GET",
	            "url": `/api/admin/orders/${year}/${customer}/${provider}/${owner}/${groups}/${status}/${from}/${to}/${orderbuy}`,
	            /* "url": "/api/admin/orders/"+year+"/"+customer+"/"+provider+"/"+owner+"/"+status, */
	            "error": function(reason){
	                console.log(reason);
	            }
	        },
	        columns: [
	            {data: 'created_at', name: 'orders.created_at', className: 'd-none' },     //Columna oculta
	            {data: 'num', name: 'orders.order_num', className: 'text-right'},
	            // {data: 'order_old_num', name: 'orders.order_old_num', className: 'text-right'},
	            {data: 'manager', name: 'users.name'},
	            {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
	            {data: 'company', name: 'companies.name'},
	            {data: 'budget_num', name: 'orders.budget_num', className: 'text-right'},
	            {data: 'order_parent', name: 'orders.order_num', className: 'text-center'},
	            {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
	            {data: 'name', name: 'orders.name'},
	            {data: 'amount', className: 'text-right'},
	            {data: 'fechaEntrega', className: 'text-right'},
	            //{data: 'fechaEntregaPrevista', className: 'text-right'},
	            {data: 'cost_center', name: 'cost_centers.name'},
	            {data: 'estado', name: 'estado'},
	            {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
	        ],
	        // Indicamos que columnas se tienen que ocultar
	        columnDefs: [
	            {
	                targets: 0,
	                visible: false,
	                searchable: false
	            },
	            {
	                targets: 3,
	                visible: false,
	                searchable: true
	            }
	        ],
	        footerCallback: function(row, data, start, end, display){
	            var api = this.api(), data;
	            var json = tableOrders.ajax.json();
	            var currency = '€';

	            // Total de la columna importe:
	            $(api.column(9).footer()).html(
	                `${json.total}${currency}`
	            );

	            // -- DATOS INSERVIBLES --
	            // api.rows().every(function(){
	            //     var data = this.data();
	            // });

	            //Separamos el importe de la moneda:
	            // var intVal = function(i){
	            //     return typeof i === 'string'? 
	            //     i.replace(/[\'+currency+',]/g, '')*1 :
	            //     typeof i === 'number' ?
	            //         i : 0;
	            // };

	            //Total:
	            // total = api
	            //     .column(8, { search: 'applied' })
	            //     .data()
	            //     .reduce(function(a, b){
	            //         return intVal(a) + intVal(b);
	            //     }, 0);
	            // $(api.column(8).footer()).html(
	            //      total.toFixed(2)+currency   
	            // );

	            //$(api.column(8).footer()).html(json.amount);
	        }
	        // rowCallback: function( row, data, index ) {
	        //     var api = this.api(), data;
	        //     var sum = 0;
	        //     sum += data.amount;
	        //     $(api.column(8).footer()).html(sum);
	        // }
	    });

	    //Suma importes:
	    // var total_amount = tableOrders.column(8).data().sum();
	    // $('#ordersAmount').text(total_amount);
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
