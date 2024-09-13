@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="deliveries">
<input type="hidden" id="myCustomer" value="@if($customer) {{ $customer->id }} @endif">
<input type="hidden" id="myYear" value="{{ $year }}">
<input type="hidden" id="myStatus" value="@if($status){{ $status }}@else{{ 0 }}@endif">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('deliveries.create')
			<a class="btn btn-info" href="{{ route('deliveries.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.entrega_nueva')) !!}</a>
		@endcan
	</div>
</div>

{{-- Filtros --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('deliveries.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Selector de cliente --}}
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.albaranes_filtro_texto') !!}</p>
						</div>
						
						@include('admin.partials.selector-customer', ['all' => true, 'selected' => $customer? $customer->id:false])
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@php
						$selected_year = $year? $year:date('Y');
						@endphp
						@include('admin.partials.selector-year', ['selected_year' => $selected_year])

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
						<div class="col-lg-2">
							@php
							//Rehacemos el array de estados para incluir en primer lugar la opción de Todos. Descartamos el uso de la función array_unshift pues resetea los índices del resto de opciones, lo cual no interesa.
							$delivery_status_[0] = trans('textos.todos');
							foreach($delivery_status as $k => $os){
								$delivery_status_[$k] = $os;
							}
							@endphp

							<x-select name="status" class="select2" :options="$delivery_status_" label="estado_selec" value="{{ $status }}"/>
						</div>

						<x-btn-submit name="submit_filter" inline label="{{ trans('textos.buscar') }}"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-deliveries" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.entregas')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'orders') active @endif">
			  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
			  		{{-- Entregas --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-deliveries">
						@include('admin.deliveries.partials.deliveries-deliveries')	
					</div>

					{{-- Pedidos --}}
					<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-orders">
						@include('admin.deliveries.partials.deliveries-orders')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Deliveries:
    if($('#tblDeliveries').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val() != ''? $('#myCustomer').val():false;
        var year = $('#myYear').val();
        var status = $('#myStatus').val() && $('#myStatus').val() != ''? $('#myStatus').val():false;

        //Filters:
        $('#tblDeliveries thead tr').clone(true).appendTo('#tblDeliveries thead');
        $('#tblDeliveries thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 2 || i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
            }

            $('input', this).on('keyup change', function(){
                if(tableDeliveries.column(i).search() !== this.value){
                    tableDeliveries
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableDeliveries = $('#tblDeliveries').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/deliveries/"+year+"/"+customer+"/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'name'},
                {data: 'amount', className: 'text-right'},
                {data: 'date', className: 'text-right'},
                {data: 'status'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Deliveries Orders:
    if($('#tblDeliveriesOrders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblDeliveriesOrders thead tr').clone(true).appendTo('#tblDeliveriesOrders thead');
        $('#tblDeliveriesOrders thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
            }

            $('input', this).on('keyup change', function(){
                if(tableDeliveriesOrders.column(i).search() !== this.value){
                    tableDeliveriesOrders
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableDeliveriesOrders = $('#tblDeliveriesOrders').DataTable({
            serverSide: true,
            processing: true,
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/deliveries-orders",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'name'},
                {data: 'company'},
                {data: 'id'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    var num = row['id'];
                    if(row['order_num']){
                        num = row['order_num'];
                    }else if(row['order_old_num']){
                        num = row['order_old_num'];
                    }

                    var html = '';
                    if(row['editar']){
                        html += '<a target="_blank" href="/admin/orders/'+row['id']+'/edit">';
                    }
                    html += num;
                    if(row['editar']){
                        html += '</a>';
                    }
                    return html;
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    console.log(row['deliveries']); 
                    var estado = '<select class="form-control select-xs comboEstadoGroup select2-dt">';
                        estado += '<option value="">'+txtEntregaSelec+'</option>';
                        for(j=0; j<row['deliveries'].length; j++){
                            estado += '<option value="" data-id="'+row['deliveries'][j].id+'">'+row['deliveries'][j].id+'</option>';
                        }
                    estado += '</select>';

                    return estado;
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Estado múltiple:
                        //estado = multipleStatus(row['estados'], row['status'], row['id']);

                        //Editar:
                        editar = '<a target="_blank" href="/admin/orders/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/deliveries/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEntregaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return editar+' '+eliminar;
                    }
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection