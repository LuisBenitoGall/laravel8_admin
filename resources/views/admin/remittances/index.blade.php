@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="remittances">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('remittances.create')
			{{-- Nueva remesa de cobros --}}
			<a class="btn btn-info" href="{{ route('remittances.create', 'collection') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.remesa_nueva_cobros')) !!}</a>

			{{-- Nueva remesa de pagos --}}
			<a class="btn btn-info" href="{{ route('remittances.create', 'payment') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.remesa_nueva_pagos')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblRemittances">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt50">{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_creacion')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_envio')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.liquidacion_fecha')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.entidad')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}</th>
					<th class="wdt50">{!! ucfirst(trans('textos.creado_por')) !!}</th>
					<th class="wdt50">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Remittances:
    if($('#tblRemittances').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblRemittances thead tr').clone(true).appendTo('#tblRemittances thead');
        $('#tblRemittances thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 10){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableRemittances.column(i).search() !== this.value){
                    tableRemittances
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableRemittances = $('#tblRemittances').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/remittances",
                "error": function(reason){
                console.log(reason);
            }
            },
            columns: [
                {data: 'num', className: 'capitalize text-right'},
                {data: 'type'},
                {data: 'created_at', className: 'text-right'},
                {data: 'send_date', className: 'text-right'},
                {data: 'paying_date', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'entity'},
                {data: 'bank_account', className: 'text-right'},
                {data: 'created_by'},
                {data: 'estado', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection