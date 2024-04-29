@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('contracts.create')
			<a class="btn btn-info" href="{{ route('contracts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contrato_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblContracts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.contrato')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!} <small>({!! trans('textos.dias') !!})</small></th>
					<th class="text-center">{!! ucfirst(trans('textos.incluye_vacaciones')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Contracts:
    if($('#tblContracts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblContracts').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/contracts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'duration', className: 'text-right'},
                {data: 'with_holidays', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['with_holidays']){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtIncluyeVacaciones+'"></i>';
                    }else{
                        return '<i class="fa fa-times text-danger" rel="tooltip" title="'+txtSinVacaciones+'"></i>';
                    }
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="contracts/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/contracts/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="contracts/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtContratoEliminar+'" data-msg="'+txtContratoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection