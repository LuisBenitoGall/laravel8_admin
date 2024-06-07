@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo procedimiento --}}
		@can('procedures.create')
    		<a href="{{ route('procedures.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.procedimiento_nuevo')) !!}
    		</a>
        @endcan

	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProcedures">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
					<th>{!! ucfirst(trans('textos.procedimiento')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.ultima_revision')) !!}</th>
					<th>{!! ucfirst(trans('textos.responsable')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Procedures:
    if($('#tblProcedures').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblProcedures').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/procedures",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'procedure_num', className: 'capitalize'},
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'last_revision', className: 'text-right'},
                {data: 'id'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 4,
                "render": function(data, type, row){
                    return row['nombre']+' '+row['surname'];
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    // if(row['editar']){
                    //     var checked = (row['status'] == 1)? 'checked':'';
                    //     estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                    //         estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="procedures/status"></span>';
                    //     estado += '</label>';
                    // }

                    //Imprimir:
                    var print = '<a target="_blank" href="/admin/procedure-revisions/'+row['revisionId']+'/print" class="btn btn-xs btn-info"><i class="fa fa-print" rel="tooltip" title="'+txtDocumentoVer+'"></i></a>';

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="procedures/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="procedures/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtProcedimientoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+print+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection