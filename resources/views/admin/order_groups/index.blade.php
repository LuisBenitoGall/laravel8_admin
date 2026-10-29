@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.create')
			<a href="{{ route('order-groups.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.agrupacion_nueva')) !!}
    		</a>
    	@endcan
    </div>
</div>

<input type="hidden" id="dtController" value="orders">

{{-- Formulario filtro --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('order-groups.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
				
					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.selec_cliente_grupo_pedidos') !!}</p>
						</div>

						{{-- Selector de cliente --}}
						@include('admin.partials.selector-customer', ['all' => false, 'selected' => '', 'required' => false])

						{{-- Agrupación --}}
						<label for="og_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agrupacion_num')) !!} </label>
						<div class="col-lg-2">
							<x-input type="text" name="og_id" autocomplete="off" maxlength="25"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@if($customer)
	<input type="hidden" id="myCustomer" value="{{ $customer->id }}">
	<input type="hidden" id="myYear" value="{{ $year }}">	

	<div class="row">
		<div class="col-12 mb-3">
			<h3>
				{!! trans('textos.agrupacion_pedidos') !!} 
				<b>{{ $customer->name }}</b> 
			</h3>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblOrderGroups">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th>
						<th class="wdt200">{!! ucfirst(trans('textos.agrupacion')) !!}</th>
						<th class="wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th> 
						<th class="text-center wdt50">{!! ucfirst(trans('textos.bolsa')) !!}</th> 
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt150">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>

@else
	<div class="row">
		<div class="col-12 text-center pt-4">
			<h2>{!! trans('textos.filtra_agrupaciones') !!}</h2>
		</div>
	</div>	
@endif

@push('scripts')
<script>
$(function(){
	//Tabla Order Groups:
    if($('#tblOrderGroups').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val();
        var year = $('#myYear').val();

        //Filters:
        // $('#tblOrderGroups thead tr').clone(true).appendTo('#tblOrderGroups thead');
        // $('#tblOrderGroups thead tr:eq(1) th').each(function(i){
        //     var title = $(this).text();
        //     if(i == 7){
        //         $(this).html('');
        //     }else{
        //         $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
        //     }
     
        //     $('input', this).on('keyup change', function(){
        //         if(tableOrderGroups.column(i).search() !== this.value){
        //             tableOrderGroups
        //                 .column(i)
        //                 .search(this.value)
        //                 .draw();
        //         }
        //     });
        // });
        
        var tableOrderGroups = $('#tblOrderGroups').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-groups/"+customer+"/"+year,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', name: 'num', className: 'text-right'},
                {data: 'order_old_num', name: 'order_old_num', className: 'text-right'},
                {data: 'name', name: 'orders.name'},
                {data: 'purchase', name: 'purchase'},
                {data: 'alta', name: 'alta', className: 'text-right'},
                {data: 'group_amount', className: 'text-right'},
                {data: 'bag_amount', className: 'text-right'},
                //{data: 'costCenter'},
                //{data: 'workplace'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', name: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection