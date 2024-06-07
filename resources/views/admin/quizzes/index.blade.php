@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('quizzes.create')
    		<a href="{{ route('quizzes.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.concurso_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuizzes">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.concurso')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th>{!! ucfirst(trans('textos.propietario')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Quizzes:
    if($('#tblQuizzes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        var tableQuizzes = $('#tblQuizzes').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quizzes",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'quizzes.name'},
                {data: 'alta', className: 'text-right'},
                {data: 'description', name: 'quizzes.description'},
                {data: 'owner'},
                {data: 'acciones', className: 'text-right'}
            ]
            /*columnDefs: [
                {"targets": 2,
                "render": function(data, type, row){
                    return row['description'];
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtConcursoOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/quizzes/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/quizzes/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar += '<form method="POST" action="quizzes/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtConcursoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ] */
        });
    }
});
</script>
@endpush

@endsection