@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="companyId" value="{{ $company->id }}">
<input type="hidden" id="businessRelationship" value="{{ $business_relationship }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@if($business_relationship == 'companies')
			<a href="{{ route('companies.edit', $company) }}" class="btn btn-info">
			@php
			$label = 'volver_edicion_empresa';
			@endphp
		@else
			<a href="{{ route($business_relationship.'.edit', $company) }}" class="btn btn-info">
			@php
			$label = $business_relationship == 'customers'? 'volver_edicion_cliente':'volver_edicion_proveedor';
			@endphp	
		@endif
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.'.$label) !!}
		</a>

		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('workplaces.create', [$company, $business_relationship]) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.centro_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.centros_trabajo_de')) }} <u>{{ $company->name }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblWorkplaces">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.centro_trabajo')) !!}</th>
					<th>{!! ucfirst(trans('textos.poblacion')) !!}</th>
					<th class="text-center">Logo</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Workplaces:
    if($('#tblWorkplaces').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#companyId').val();
        var business_relationship = $('#businessRelationship').val();
        $('#companyId').remove();
        
        $('#tblWorkplaces').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/workplaces/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'Town'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/workplaces/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-building icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var principal = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        if(row['featured'] != 1){
                            estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtCentroEstado+'">';
                                estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/workplaces/status"></span>';
                            estado += '</label>';
                        }
                    
                        //Editar:
                        var editar = '<a href="/admin/workplaces/'+row['id']+'/edit/'+row['br']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Centro principal:
                        if(row['featured'] == 1){
                            principal = '<i class="fa fa-star text-warning ml-1 mr-1" rel="tooltip" title="'+txtCentroPrincipal+'"></i>';
                        }else if(row['featured'] == 0 && row['status'] == 1){
                            principal = '<a href="/admin/workplaces/'+row['id']+'/'+row['br']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtCentroPrincipalMarcar+'"><i class="fa fa-star"></i></a>';    
                        }  
                    }   

                    //Eliminar:
                    var eliminar = '';
                    //Sólo se pueden eliminar centros secundarios, no el principal:
                    if(row['featured'] == 0 && row['eliminar']){
                        eliminar += '<form method="POST" action="/admin/workplaces/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="business_relationship" value="'+row['br']+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtCentroEliminar+'" data-msg="'+txtCentroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
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