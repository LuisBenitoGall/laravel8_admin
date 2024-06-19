<div class="table-responsive">
	<table class="table table-hover dtabla" id="tblGroupMembers">
		<thead>
			<tr>
				<th>{!! ucfirst(trans('textos.miembro')) !!}</th>
				<th>{!! ucfirst(trans('textos.role')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla GroupMembers:
    if($('#tblGroupMembers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var group_id = $('#groupId').val();
        
        $('#tblGroupMembers').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/group-members/"+group_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'rol'},
                {data: 'alta', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

});
</script>
@endpush


