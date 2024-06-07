@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="provinceId" value="{{ $province->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('provinces.index', $province->country_id) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_provincias') !!}
		</a>

		@can('countries.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-town"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.poblacion_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.poblaciones_de')) }} <u>{{ ucfirst($province->name) }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblTowns">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.poblacion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- New town --}}
@include('admin.towns.partials.modal-new-town')

{{-- Edit town --}}
@include('admin.towns.partials.modal-edit-town')

@push('scripts')
<script>
$(function(){
	//Tabla Towns:
    if($('#tblTowns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var province_id = $('#provinceId').val();
        
        $('#tblTowns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/towns/"+province_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';

                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtProvinciaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/towns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditTown" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-town" data-id="'+row['id']+'" data-town="'+row['name']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/towns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPoblacionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

	//Modal editar población:
	$(document).on('click', '.btnEditTown', function(){
		var id = $(this).data('id');
		var town = $(this).data('town');
		var status = $(this).data('status');

		$('#townId').val(id);
		$('#townName').val(town);
		if(status){
			$('#townStatus').prop('checked', true);
		}
	});
});
</script>
@endpush

@endsection