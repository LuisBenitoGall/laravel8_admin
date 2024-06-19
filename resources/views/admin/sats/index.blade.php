@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="sat">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo servicio --}}
		@can('sat.create')
			<a href="{{ route('sat.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.sat_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblSats">
			<thead>
				<tr>
					{{-- Num --}}
					<th class="text-center wdt50">#</th>
					{{-- Contratante --}}
					<th class="text-center wdt150">{!! ucfirst(trans('textos.contratante')) !!}</th>
					{{-- Proveedor --}}
					<th class="text-center wdt150">{!! ucfirst(trans('textos.proveedor')) !!}</th>
					{{-- Fecha solicitado --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_inicio')) !!}</th>
					{{-- Fecha entrega --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					{{-- Importe --}}
					<th class="text-center wdt100 text-right">{!! ucfirst(trans('textos.importe')) !!}</th>
					{{-- Status --}}
					<th class="wdt50">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla SAT:
    if($('#tblSats').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.description? d.description:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="8" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        var tableSats =  $('#tblSats').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/sats",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'customer'},
                {data: 'provider'},
                {data: 'alta', className: 'text-right'},
                {data: 'delivery_date', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'estado', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblSats tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableSats.row(tr);
     
            if(row.child.isShown()){
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }else{
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });
    }
});
</script>
@endpush

@endsection
