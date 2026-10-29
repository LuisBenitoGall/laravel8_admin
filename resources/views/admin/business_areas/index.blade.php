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
			<a class="btn btn-info" href="{{ route('business-areas.create', [$company, $business_relationship]) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.area_negocio_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.areas_negocio_de')) }} <u>{{ $company->name }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBusinessAreas">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.area_negocio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Business Areas:
    if($('#tblBusinessAreas').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#companyId').val();
        var business_relationship = $('#businessRelationship').val();
        $('#companyId').remove();
        
        $('#tblBusinessAreas').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/business-areas/"+company_id+"/"+business_relationship,
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
                    var costes = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/business-areas/status"></span>';
                        estado += '</label>';

                        //Costes del área de negocio:
                        costes = '<a href="/admin/business-areas/'+row['id']+'/costs" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtCostesHistorico+'"><i class="fa fa-money-bill-alt"></i></a>';

                        //Editar:
                        editar = '<a href="/admin/business-areas/'+row['id']+'/edit/'+row['br']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="/admin/business-areas/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="business_relationship" value="'+row['br']+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtAreaNegocioEliminar+'" data-msg="'+txtAreaNegocioConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+costes+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection