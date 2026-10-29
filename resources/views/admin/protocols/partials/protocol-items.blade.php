<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla table-draggable" id="tblProtocolItems">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.titulo')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha_alta')) }}</th>
					<td>{{ ucfirst(trans('textos.autor')) }}</td>
					<th class="text-center">{{ ucfirst(trans('textos.orden')) }}</th>
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
	//Tabla Protocol Items:
    if($('#tblProtocolItems').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var protocol_id = $('#dtProtocolId').val();
        var collection = [];

        var tableProtocolItems = $('#tblProtocolItems').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            retrieve: true,
            rowReorder: {
                rowOrder: true,
                dataSrc: 'sort',
                selector: 'tr'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/protocol-items/"+protocol_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'title', name: 'protocol_items.title'},
                {data: 'created_at', className: 'text-right'},
                {data: 'author', name: 'users.name'},
                {data: 'sort', className: 'text-center'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ]
        });

        //Reordenar filas:
        tableProtocolItems.on('row-reordered', function (e, diff, edit){
            for(var i=0; i<diff.length; i++){
                var rowData = tableProtocolItems.row(diff[i].node).data();
                collection.push({
                    id: rowData['id'],
                    pos: diff[i].newData    
                });

                $.ajax({
                    url: "/admin/protocol-items/reorder-items", 
                    type: "POST",
                    data: {
                        _token: tokenDT,
                        collection: collection
                    }, 
                    cache: false,
                    success: function(response){
                        collection = [];
                    }
                });
            }
        });
    }
});
</script>
@endpush