@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones --}}
		@can('units.create')
			<a class="btn btn-info" href="{{ route('units.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.unidad_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblUnits">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.unidad_venta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.simbolo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Units:
    if($('#tblUnits').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblUnits').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/units/",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'symbol'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var trans = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtUnidadOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/units/status"></span>';
                        estado += '</label>';

                        //Translations:
                        trans = linkTranslations(row['id'], 'units', txtTraducciones);

                        //Editar:
                        editar = '<a href="/admin/units/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    var eliminar = '';
                    if(row['eliminar']){
                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/units/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtUnidadVentaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+trans+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Enlace para widget de traducciones:
	function linkTranslations(objid, schema, title){
		return '<a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="'+title+'" data-toggle="modal" data-target="#modal-translations" data-id="'+objid+'" data-schema="'+schema+'"><i class="fa fa-globe"></i></a>';
	}
});
</script>
@endpush

@endsection