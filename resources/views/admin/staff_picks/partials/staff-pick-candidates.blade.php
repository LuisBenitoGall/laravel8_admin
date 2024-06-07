<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblStaffPickCandidates">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.candidato')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.valoracion')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha_alta')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Staff Pick Candidates:
    if($('#tblStaffPickCandidates').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var process_id = $('#processId').val();
        
        $('#tblStaffPickCandidates').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/staff-pick-candidates/"+process_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['name']+' '+row['surname'];
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    var stars = '<div class="rating">';
                    for(i=1; i<=5; i++){
                        var active = i <= row['assessment']? 'active':'';
                        stars += '<i class="fa fa-star '+active+'"></i>';
                    }  
                    stars += '</div>';  
                    return stars;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var editar = '';
                    if(row['editar']){
                        //Editar:
                        editar = '<a href="/admin/staff-pick-candidates/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['editar']){
                        eliminar = '<form method="POST" action="/admin/staff-pick-candidates/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtCandidatoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return editar+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush


