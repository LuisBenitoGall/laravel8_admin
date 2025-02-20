@extends('admin.partials.layout')

@section('content')

{{-- <input type="hidden" id="dtYear" value="{{ $year }}">
<input type="hidden" id="dtYearTo" value="{{ $year_to }}"> --}}
<input type="hidden" id="dtFrom" value="{{ $from }}">
<input type="hidden" id="dtTo" value="{{ $to }}">
<input type="hidden" id="dtWorkplace" value="{{ $workplace_id }}">
<input type="hidden" id="dtCurrency" value="{{ $currency->symbol }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.index')
			<a href="{{ route('orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_pedidos') !!}
			</a>
		@endcan
	</div>
</div>

{{-- Filtro --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				@php
				$ruta = $order_type == 'compras'? 'control-panel-purchases-year':'control-panel-sales-year';
				@endphp

				<form method="post" autocomplete="off" action="{{ route('orders.'.$ruta) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="order_type" id="orderType" value="{{ $order_type }}">

					<div class="form-group row">
						{{-- Año desde. 20/02/2025 Se modifica el filtro de años por el de intervalo de fechas. --}}
						{{-- @include('admin.partials.selector-year', ['selected_year' => $year]) --}}

                        {{-- Año hasta --}}
                        {{-- <label for="year_to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.any_hasta')) !!} </label> --}}
                        {{-- <div class="col-lg-2">
                            @php
                            $max_year = date('Y')+1;
                            $years = [];
                            for($i=$max_year; $i>=config('constants.MIN_YEAR_'); $i--){
                                $years[$i] = $i;
                            }
                            @endphp

                            <x-select id="selectorYearTo" name="year_to" class="select2" label="selecciona_any" :options="$years" value="{{ $year_to }}"/>
                        </div> --}}

                        {{-- Desde --}}
                        <label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
                        <div class="col-lg-2">
                            @php
                            $from_ = $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
                            @endphp

                            <x-date-picker name="from" value="{{ $from_ }}" :format="$languages[config('app.locale')][4]"/> 
                        </div>

                        {{-- Hasta --}}
                        <label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
                        <div class="col-lg-2">
                            @php
                            $to_ = $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
                            @endphp

                            <x-date-picker name="to" value="{{ $to_ }}" :format="$languages[config('app.locale')][4]"/> 
                        </div>                        

						{{-- Centros de trabajo --}}
						@if(count($workplaces) > 1)
							<label for="workplace" class="col-2 col-form-label">{{ ucfirst(trans('textos.centro_trabajo')) }}</label>
	                    	<div class="col-2">
	                    		<x-select name="workplace" class="select2" label="centro_trabajo_selec" :options="$workplaces" value="{{ $workplace_id }}"/>
	                    	</div>
	                    @elseif(count($workplaces) == 1)
	                    	@foreach($workplaces as $i => $wk)
	                    		<input type="hidden" name="workplace" value="{{ $i }}">
	                    	@endforeach
	                    @endif
                    </div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		@php
		$tableId = $order_type == 'compras'? 'tblOrdersControlPanelPurchases':'tblOrdersControlPanelSales';
		@endphp

		<table class="table table-hover dtabla" id="{{ $tableId }}">
			<thead>
				<tr>
					<th class="wdt150">{!! ucfirst(trans('textos.facturacion_prevista')) !!}</th>
					
					@php
					$lbl1 = $order_type == 'compras'? 'proveedor':'cliente';
					@endphp
					<th class="wdt200">{!! ucfirst(trans('textos.'.$lbl1)) !!}</th>
					{{-- <th class="wdt150">{!! ucfirst(trans('textos.area_negocio')) !!}</th> --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.pedido_num_abrv')) !!}</th>
                    @if($order_type == 'ventas')
                        <th class="text-center wdt100">{!! ucfirst(trans('textos.proyecto')) !!}</th>
                    @endif
					<th class="text-center wdt100">OC</th>
					<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.manager')) !!}</th>
					<th class="d-none">&nbsp;</th>
					<th class="wdt150">{!! ucfirst(trans('textos.centro_trabajo')) !!}</th>
						{{-- <th class="text-center wdt150">{!! ucfirst(trans('textos.cliente_final')) !!}</th> --}}
					<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.a_facturar')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.facturado')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td></td>
					<td></td>
                    @if($order_type == 'ventas')
                        <td></td>
                    @endif
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
						{{-- <td></td> --}}
					<td class="text-right"><b>{{ trans('textos.total') }}</b></td>
					<td class="text-right"></td>
					{{-- <td></td> --}}
					{{-- <td class="text-right"><b>{{ trans('textos.a_facturar') }}</b></td> --}}
					<td class="text-right"></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>	
			</tfoot>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Orders Control Panel Purchases:
    if($('#tblOrdersControlPanelPurchases').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        // var year = $('#dtYear').val();
        // var year_to = $('#dtYearTo').val();
        var from = $('#dtFrom').val();
        var to = $('#dtTo').val();
        var workplace = $('#dtWorkplace').val();
        var currency = $('#dtCurrency').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrdersControlPanelPurchases_'+window.location.pathname));

        $('#tblOrdersControlPanelPurchases thead tr').clone(true).appendTo('#tblOrdersControlPanelPurchases thead');
        $('#tblOrdersControlPanelPurchases thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 

            if(i == 12){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos.
                if (i == 5) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableOrdersCPPurchases
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableOrdersCPPurchases
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }
            }
         
            $('input', this).on('keyup change', function(){
                if(tableOrdersCPPurchases.column(i).search() !== this.value){
                    tableOrdersCPPurchases
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableOrdersCPPurchases = $('#tblOrdersControlPanelPurchases').DataTable({
            order: [[ 0, "desc" ]],
            serverSide: false,  //01/09/22: en false para permitir la suma de totales del footer.
            processing: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/orders-control-panel-purchases/"+from+"/"+to,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', name: 'order_previsions.month'},
                {data: 'provider', name: 'companies.name'},
                //{data: 'business_area', name: 'business_areas.name'},
                {data: 'order_num', name: 'orders.order_num', className: 'text-right'},
                {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
                {data: 'name', name: 'companies.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none' },
                {data: 'workplace', name: 'workplaces.name'},
                {data: 'amount', name: 'amount', className: 'text-right'},
                {data: 'a_facturar', name: 'a_facturar', className: 'text-right'},
                {data: 'invoice_amount', name: 'invoice_amount', className: 'text-right'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;
                var json = api.ajax.json();

                var currency = false;
                api.rows().every(function(){
                    var data = this.data();
                });

                if(!currency) currency = '€';

                //Separamos el importe de la moneda:
                var intVal = function(i){
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                // total = api
                // .column(8)
                // .data()
                // .reduce(function(a, b){
                //     return intVal(a) + intVal(b);
                // }, 0);

                //Total:
                total = api
                .column(8, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(8).footer()).html(
                    total.toFixed(2)+currency   
                );

                //Total por filtro aplicado:
                a_facturar = api
                .column(9, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(9).footer()).html(
                    a_facturar.toFixed(2)+currency    
                );
            }
        });
    }

    //Tabla Orders Control Panel Sales:
    if($('#tblOrdersControlPanelSales').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        // var year = $('#dtYear').val();
        // var year_to = $('#dtYearTo').val();
        var from = $('#dtFrom').val();
        var to = $('#dtTo').val();
        var workplace = $('#dtWorkplace').val();
        var currency = $('#dtCurrency').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrdersControlPanelSales_'+window.location.pathname));

        $('#tblOrdersControlPanelSales thead tr').clone(true).appendTo('#tblOrdersControlPanelSales thead');
        $('#tblOrdersControlPanelSales thead tr:eq(1) th').each(function(i){
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
                if (i == 6) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableOrdersCPSales
                                .column(6)
                                .search(fullname[0])
                                .column(7)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableOrdersCPSales
                                .column(6)
                                .search(fullname[0])
                                .column(7)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }
            }
         
            $('input', this).on('keyup change', function(){
                if(tableOrdersCPSales.column(i).search() !== this.value){
                    tableOrdersCPSales
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableOrdersCPSales = $('#tblOrdersControlPanelSales').DataTable({
            order: [[ 0, "desc" ]],
            serverSide: false,  //01/09/22: en false para permitir la suma de totales del footer y el filtrado por la columna fecha.
            processing: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/orders-control-panel-sales/"+from+"/"+to+'/'+workplace,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', name: 'order_previsions.month'},
                {data: 'customer', name: 'companies.name'},
                //{data: 'business_area', name: 'business_areas.name'},
                {data: 'order_num', name: 'orders.order_num', className: 'text-right'},
                {data: 'project', name: 'projects.project_num', className: 'text-right'},
                {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
                {data: 'name', name: 'companies.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none' },
                {data: 'workplace', name: 'workplaces.name'},
                // {data: 'finalCustomer', name: 'companies.name'},
                {data: 'amount', name: 'amount', className: 'text-right'},
                {data: 'a_facturar', name: 'a_facturar', className: 'text-right'},
                {data: 'invoice_amount', name: 'invoice_amount', className: 'text-right'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;
                var json = api.ajax.json();

                var currency = false;
                api.rows().every(function(){
                    var data = this.data();
                });

                if(!currency) currency = '€';

                //Separamos el importe de la moneda:
                var intVal = function(i){
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                // total = api
                // .column(8)
                // .data()
                // .reduce(function(a, b){
                //     return intVal(a) + intVal(b);
                // }, 0);

                //Total:
                total = api
                .column(9, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(9).footer()).html(
                    total.toFixed(2)+currency   
                );

                //Total por filtro aplicado:
                a_facturar = api
                .column(10, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(10).footer()).html(
                    a_facturar.toFixed(2)+currency    
                );
            }
        });
    }

    //Selector de año:
    $('#selectorYearTo').on('change', function(){
        $year_from = $('#selectorYear').val();
        $year_to = $('#selectorYearTo').val();

        //El año fin no puede ser inferior al año inicio:
        if($year_to < $year_from){
            swal.fire({
                title: "{{ trans('textos.any_hasta_aviso') }}",
                text: "",
                icon: 'warning',
                iconColor: color_warning,
                showCancelButton: false,
                confirmButtonColor: color_danger,
                cancelButtonColor: color_danger,
                confirmButtonText: txtCancelar,
                cancelButtonText: txtCancelar
            }).then((result) => {
                if(result.isConfirmed){
                    let arr = [$year_from];
                    $('#selectorYearTo').val(arr);
                    $('#selectorYearTo').trigger('change');    
                }
            });
        }
    });
});
</script>
@endpush

@endsection