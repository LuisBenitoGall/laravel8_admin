<div class="table-responsive">
	<table class="table table-hover dtabla" id="tblQuestionAnswers">
		<thead>
			<tr>
				<th>{!! ucfirst(trans('textos.respuesta')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.valor')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Question Answers:
    if($('#tblQuestionAnswers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var question_id = $('#questionId').val();
        
        $('#tblQuestionAnswers').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/question-answers/"+question_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'value', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtRespuestaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/answers/status"></span>';
                        estado += '</label>';

                        //Editar:
                        var editar = '<a href="#" data-toggle="modal" data-target="#modal-edit-answer" class="btn btn-xs btn-info btnEditAnswer" rel="tooltip" title="'+txtEditar+'" data-id="'+row['id']+'" data-name="'+row['name']+'" data-answer="'+row['answer']+'" data-points="'+row['points']+'" data-tf="'+row['is_true']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        var eliminar = '<form method="POST" action="/admin/answers/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtRespuestaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return estado+' '+editar+' '+eliminar;
                    
                    }else{
                        return '';
                    }
                }
                }
            ]
        });
    }
});
</script>
@endpush