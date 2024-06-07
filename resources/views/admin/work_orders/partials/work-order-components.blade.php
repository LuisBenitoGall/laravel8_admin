<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblWorkOrderComponents">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.componente')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad_suministrada')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad_consumida')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad_retornada')) }}</th>
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
	//Tabla Work Order Components:
    if($('#tblWorkOrderComponents').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var work_order = $('#workOrderId').val();

         $('#tblWorkOrderComponents').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/work-order-components/"+work_order,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', className: 'capitalize'},
                {data: 'supplied_qty', className: 'text-right'},
                {data: 'used_qty', className: 'text-right'},
                {data: 'returned_qty', className: 'text-right'}, 
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush