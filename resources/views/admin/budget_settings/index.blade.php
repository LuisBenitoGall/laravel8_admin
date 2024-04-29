@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-sm-12">
        {{-- Acciones: --}}
		@can('budget-settings.create')
			<a class="btn btn-info" href="{{ route('budget-settings.create') }}">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.plantilla_nueva')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBudgetSettings">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
	                <th>{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
	                <th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Budget Settings:
    if($('#tblBudgetSettings').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetSettings thead tr').clone(true).appendTo('#tblBudgetSettings thead');
        $('#tblBudgetSettings thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetSettings.column(i).search() !== this.value){
                    tableBudgetSettings
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableBudgetSettings = $('#tblBudgetSettings').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var principal = '';
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPlantillaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="budget-settings/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="budget-settings/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Plantilla principal:
                    if(row['featured'] == 1){
                        principal = '<i class="fa fa-star text-warning ml-1 mr-1" rel="tooltip" title="'+txtPlantillaPredefinida+'"></i>';
                    }else if(row['featured'] == 0 && row['status'] == 1 && row['editar']){
                        principal = '<a href="budget-settings/'+row['id']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtPlantillaPredefinir+'"><i class="fa fa-star"></i></a>';    
                    }  

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="budget-settings/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPlantillaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+principal+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection