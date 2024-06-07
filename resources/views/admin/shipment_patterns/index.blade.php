@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('shipment-patterns.create')
			<a class="btn btn-info" href="{{ route('shipment-patterns.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.patron_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblShipmentPatterns">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
					<th>{!! ucfirst(trans('textos.patron')) !!}</th>
					<th>{!! ucfirst(trans('textos.reinicio_anual')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Shipment Patterns:
    if($('#tblShipmentPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblShipmentPatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/shipment-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
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
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="shipment-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="shipment-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="shipment-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronAlbaranConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
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