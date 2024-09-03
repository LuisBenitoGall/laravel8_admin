@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('vehicles.create')
			<a class="btn btn-info" href="{{ route('vehicles.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.vehiculo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblVehicles">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.vehiculo')) !!}</th>
					<th>{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th>{!! ucfirst(trans('textos.matricula')) !!}</th>
					<th>{!! ucfirst(trans('textos.marca')) !!}</th>
					<th>{!! ucfirst(trans('textos.modelo')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.imagen')) !!}</th>
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
	//Tabla Vehicles:
    if($('#tblVehicles').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblVehicles thead tr').clone(true).appendTo('#tblVehicles thead');
        $('#tblVehicles thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5 || i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableVehicles.column(i).search() !== this.value){
                    tableVehicles
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableVehicles = $('#tblVehicles').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/vehicles",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'type'},
                {data: 'car_license'},
                {data: 'marca'},
                {data: 'model'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    var html = '';
                    if(row['editar']){
                        html += '<a href="/admin/vehicles/'+row['id']+'/edit">';
                    }
                    html += row['name'];
                    if(row['editar']){
                        html += '</a>';
                    }
                    return html;
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['Image']){
                        var img = '<img src="/img/vehicles/'+row['Image']+'" width="36">';    
                    }else{
                        var img = '';
                    }
                    return img;    
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtVehiculoOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="vehicles/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/vehicles/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/vehicles/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtVehiculoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return '<span style="display: none">'+row['status']+'</span>'+estado+' '+editar+' '+eliminar;
                    }
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection