<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblWorkOrderProducts">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.producto')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad_prevista')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad_producida')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.lotes')) }}</th>
					<th>{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Work Order Products:
    if($('#tblWorkOrderProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var work_order = $('#workOrderId').val();

         $('#tblWorkOrderProducts').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/work-order-products/"+work_order,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', className: 'capitalize'},
                {data: 'planned_qty', className: 'text-right'},
                {data: 'produced_qty', className: 'text-right'},
                {data: 'batches'}, 
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush