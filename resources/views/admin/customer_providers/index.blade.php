@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@if($tabla == 'tblCustomers')
			@php
			$texto = 'cliente_nuevo';
			$url = 'customers.create';

            $texto2 = 'importar_clientes';
            $url2 = 'customers.import';
            $return = 'customers';

            $texto3 = 'exportar_clientes';
			@endphp
		@else
			@php
			$texto = 'proveedor_nuevo';
			$url = 'providers.create';	

            $texto2 = 'importar_proveedores';
            $url2 = 'providers.import';
            $return = 'providers';

            $texto3 = 'exportar_proveedores';
			@endphp
		@endif

		@can('companies.edit')
			<a href="{{ route($url) }}" class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.'.$texto)) !!}</a>

            {{-- Importar .xls --}}
            <a href="{{ route($url2) }}" class="btn btn-info"><i class="fa fa-file-import"></i> {!! ucfirst(trans('textos.'.$texto2)) !!}</a>

            {{-- Exportar valores --}}
            @if(count(session('companies')) > 1)
                <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-export-values">
                    <i class="fa fa-file-export"></i> {!! ucfirst(trans('textos.'.$texto3)) !!} 
                </a>
            @endif
        @endcan
	</div>
</div>

<div class="row">
    <div class="table-responsive">
    	<table class="table table-hover dtabla" id="{{ $tabla }}">
    		<thead>
    			<tr>
    				<th>{!! ucfirst(trans('textos.razon_social')) !!}</th>
                    <th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
    				<th>NIF</th>
    				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
    			</tr>
    		</thead>
    	</table>
    </div>
</div>

{{-- Modals --}}
@php
$modal_action = $tabla == 'tblCustomers'? 'customers.export':'providers.export';
@endphp
@include('admin.partials.modal-export-values', ['action' => $modal_action])

{{-- Editar configuración de cliente: --}}
@include('admin.customer_providers.partials.modal-edit-config')

{{-- New responsable: --}}
@include('admin.customer_providers.partials.modal-add-manager')

@push('scripts')
<script>
$(document).ready(function(){
    //Modal nuevo responsable cliente o proveedor.
    $(document).on('click', '.btnAddManager', function(){
        var id = $(this).data('id');
        var company = $(this).data('company');

        $('#newResponsable b').text(company);
        $('#cpCompanyId').val(id);
    });

    //Modal edición configuraciones de cliente.
    $(document).on('click', '.btnCustomerConfig', function(){
        var id = $(this).data('customer');
        var ba_id = $(this).data('baid');
        var workplace_id = $(this).data('workplaceid');
        var cc_id = $(this).data('ccid');
        var discount = $(this).data('discount');

        if(id){
            $('#configCustomer').val(id);
            $.get("/admin/customers/"+id+"/customer-config", function(response, state){
                if(response){
                    //Empresa:
                    $('#modal-edit-config h4 b').text(response.company);

                    //Business areas:
                    $("#configBusinessArea option").remove();
                    $("#configBusinessArea").append('<option value=""></option>');
                    for(i=0; i<response.areas.length; i++){
                        $("#configBusinessArea").append('<option value="'+response.areas[i].id+'">'+response.areas[i].name+'</option>');    
                    }
                    //valor por defecto si sólo existe 1 registro:
                    var baId = response.areas.length != 1? ba_id:response.areas[0].id;
                    $('#configBusinessArea').val(baId);
                    $('#configBusinessArea').trigger('change');

                    //Workplaces:
                    $("#configWorkplace option").remove();
                    $("#configWorkplace").append('<option value=""></option>');
                    for(i=0; i<response.workplaces.length; i++){
                        $("#configWorkplace").append('<option value="'+response.workplaces[i].id+'">'+response.workplaces[i].name+'</option>');    
                    }
                    //valor por defecto si sólo existe 1 registro:
                    var workplaceId = response.workplaces.length != 1? workplace_id:response.workplaces[0].id;
                    $('#configWorkplace').val(workplaceId);
                    $('#configWorkplace').trigger('change');

                    //Cost Centers:
                    $("#configCostCenter option").remove();
                    $("#configCostCenter").append('<option value=""></option>');
                    for(i=0; i<response.centers.length; i++){
                        $("#configCostCenter").append('<option value="'+response.centers[i].id+'">'+response.centers[i].name+'</option>');    
                    }
                    //valor por defecto si sólo existe 1 registro:
                    var ccId = response.centers.length != 1? cc_id:response.centers[0].id;
                    $('#configCostCenter').val(ccId);
                    $('#configCostCenter').trigger('change');

                    //Descuento:
                    $('#configDiscount').val(discount);
                }  
            });
        } 
    });
});
</script>
@endpush

@endsection