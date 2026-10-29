@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblReceivings">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.recepcion_fecha_prevista')) !!}</th>
					<th>{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th>{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th>{!! ucfirst(trans('textos.albaran')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Receivings:
    if($('#tblReceivings').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="5" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableReceivings = $('#tblReceivings').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/receivings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'estimated_reception_date', name: 'deliveries.estimated_reception_date', className: 'text-right'},
                {data: 'provider', name: 'companies.name'},
                {data: 'orderNum', name: 'orders.purchase_num'},
                {data: 'deliveryNum', name: 'deliveries.delivery_num'},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblReceivings tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableReceivings.row(tr);
     
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