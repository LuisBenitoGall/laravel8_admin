@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuizEditions">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.edicion')) !!}</th>
					<th>{!! ucfirst(trans('textos.concurso')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.logo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Quiz Editions:
    if($('#tblQuizEditions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblQuizEditions thead tr').clone(true).appendTo('#tblQuizEditions thead');
        $('#tblQuizEditions thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3 || i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableQuizEditions.column(i).search() !== this.value){
                    tableQuizEditions
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableQuizEditions = $('#tblQuizEditions').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-editions",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'quiz'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'start_', className: 'text-right'},
                {data: 'finish_', className: 'text-right'},
                {data: 'time', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 1,
                "render": function(data, type, row){
                    return '<a target="_blank" href="/admin/quizzes/'+row['quiz_id']+'/edit">'+row['quiz']+'</a>';
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/quizzes/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-puzzle-piece icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['start']+'</span>'+row['start_'];
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['finish']+'</span>'+row['finish_'];
                }
                },
                {"targets": 6,
                "render": function(data, type, row){
                    if(row['time'] > 0){
                        return row['time']/60+' '+txtMinutos;
                    }else{
                        return '';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="/admin/quiz-editions/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = ''; 
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="/admin/quiz-editions/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEdicionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
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

@endsection