<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProtocolUsers">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.usuario')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha_presentacion')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.usuario_firma')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Protocol Users:
    if($('#tblProtocolUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var protocol_id = $('#dtProtocolId').val();

        var tableProtocolUsers = $('#tblProtocolUsers').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/protocol-users/"+protocol_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: 'users.name'},
                {data: 'created_at', className: 'text-right'},
                {data: 'signed_at', className: 'text-right'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush