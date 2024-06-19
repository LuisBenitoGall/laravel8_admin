@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="countryId" value="{{ $country->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('countries.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_paises') !!}
		</a>

		@can('countries.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-province"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.provincia_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.provincias_de')) }} <u>{{ ucfirst($country->name) }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProvinces">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.provincia')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- New province --}}
@include('admin.provinces.partials.modal-new-province')

{{-- Edit province --}}
@include('admin.provinces.partials.modal-edit-province')

@push('scripts')
<script>
$(function(){
	//Tabla Provinces:
    if($('#tblProvinces').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var country_id = $('#countryId').val();
        
        $('#tblProvinces').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/provinces/"+country_id,
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
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/provinces/status"></span>';
                        estado += '</label>';
                    }

                    var towns = '';
                    if(row['ver']){
                        //Poblaciones:
                        towns = '<a href="/admin/towns/'+row['id']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtPoblaciones+'"><i class="fa fa-city"></i></a>';
                    }

                    if(row['editar']){
                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditProvince" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-province" data-id="'+row['id']+'" data-province="'+row['name']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/provinces/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtProvinciaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+towns+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

	//Modal editar provincia:
	$(document).on('click', '.btnEditProvince', function(){
		var id = $(this).data('id');
		var province = $(this).data('province');
		var status = $(this).data('status');

		$('#provinceId').val(id);
		$('#provinceName').val(province);
		if(status){
			$('#provinceStatus').prop('checked', true);
		}
	});
});
</script>
@endpush

@endsection