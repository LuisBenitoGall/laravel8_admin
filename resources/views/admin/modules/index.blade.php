@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('modules.create')
			<a href="{{ route('modules.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.modulo_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblModules">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.modulo')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
	                <th class="wdt50">{!! ucfirst(trans('textos.icono')) !!}</th>
	                <th class="wdt50">{!! ucfirst(trans('textos.color')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div> 
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Modules:
    if($('#tblModules').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblModules').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/modules",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'module', className: 'capitalize'},
                {data: 'nivel'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 2,
                "render": function(data, type, row){
                    var icon = '<i class="fa fa-'+row['icon']+'"></i>';

                    return icon;
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    return '<span class="colorEx mr-2" style="background-color: '+row['color']+'"></span>';
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado, sólo para módulos opcionales:
                    if(row['level'] == 3){
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtModuloOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="modules/status"></span>';
                        estado += '</label>';
                    }else{
                        var estado = '';
                    }

                    //Translations:
                    var trans = linkTranslations(row['id'], 'modules', txtTraducciones);
                    
                    //Editar:
                    var editar = '<a href="modules/'+row['id']+'/edit" class="btn btn-xs btn-info btnModuleEdit" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="modules/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtModuloConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';
                    
                    //var eliminar = '<a href="#" class="btn btn-xs btn-danger btnDelete" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtModuloConfirmEliminar+'" data-model="modules" data-id="'+row['id']+'"><i class="fa fa-edit"></i></a>';

                    // var eliminar = '<a class="btn btn-xs btn-danger" wire:click="$emit("destroy", '+row['id']+')" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtModuloConfirmEliminar+'"><i class="fa fa-trash"></i></a>';

                    return estado+' '+trans+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection