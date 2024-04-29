@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('invoices.edit')
			<a class="btn btn-info" href="{{ route('invoice-settings.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.plantilla_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblInvoiceSettings">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
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
	//Tabla Invoice Settings:
    if($('#tblInvoiceSettings').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblInvoiceSettings').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/invoice-settings",
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
                    var checked = (row['status'] == 1)? 'checked':'';
                    var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPlantillaOnOff+'">';
                        estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="invoice-settings/status"></span>';
                    estado += '</label>';

                    //Editar:
                    var editar = '<a href="invoice-settings/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Plantilla predefinida:
                    if(row['featured'] == 1){
                        var predefinida = '<i class="fa fa-star text-warning ml-1 mr-2" rel="tooltip" title="'+txtPlantillaPredefinida+'"></i>';
                    }else{
                        var predefinida = '<a href="invoice-settings/'+row['id']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtPlantillaPredefinir+'"><i class="fa fa-star"></i></a>';    
                    }                    

                    //Eliminar:
                    var eliminar = '<form method="POST" action="invoice-settings/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPlantillaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return estado+' '+editar+' '+predefinida+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection