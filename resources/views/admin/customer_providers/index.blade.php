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
<div class="modal fade modal-edit-config" id="modal-edit-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.configuracion')) !!} <b></b></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<div class="row">
            		<p class="col-12 text-warning">{!! trans('textos.establece_por_defecto_texto') !!}</p>
            	</div>

                <form method="post" action="{{ route('customers.config') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="customer_id" id="configCustomer">

                    {{-- Área de negocio --}}
                    <div class="form-group row">
                        <label for="ba_id" class="col-lg-12">{!! ucfirst(trans('textos.area_negocio')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="ba_id" class="select2" id="configBusinessArea" :options="[]"/>
                        </div>
                    </div>

                    {{-- Centro de trabajo --}}
                    <div class="form-group row">
                        <label for="workplace_id" class="col-lg-12">{!! ucfirst(trans('textos.centro_trabajo')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="workplace_id" class="select2" id="configWorkplace" :options="[]"/>
                        	</select>	
                        </div>
                    </div>

                    {{-- Centro de coste --}}
                    <div class="form-group row">
                        <label for="cc_id" class="col-lg-12">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="cc_id" class="select2" id="configCostCenter" :options="[]"/>
                            </select>
                        </div>
                    </div>

                    {{-- Descuento sobre pedidos --}}
                    <div class="form-group row">
                        <label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.descuento_pedidos')) !!}</label>
                        <div class="col-lg-6">
                            <x-input type="text" name="discount" class="text-right set-decimals" id="configDiscount" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="customer-discount"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update_config" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
{{-- New responsable: --}}
<div class="modal fade modal-add-manager" id="modal-add-manager" tabindex="-1" role="dialog" aria-labelledby="newResponsable" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newResponsable">{!! ucfirst(trans('textos.nuevo_responsable_de')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('customers.add-responsable') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="company_id" id="cpCompanyId">
                    <input type="hidden" name="return" value="{{ $return }}">

                    <div class="form-group row">
                        {{-- Nombre del responsable --}}
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.nombre')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="name" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Apellidos --}}
                        <label for="surname" class="col-lg-12">{!! ucfirst(trans('textos.apellidos')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="surname" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Email --}}
                        <label for="email" class="col-lg-12">Email *</label>
                        <div class="col-lg-12">
                            <x-input type="email" name="email" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Puesto --}}
                        <label for="job_title" class="col-lg-12">{!! ucfirst(trans('textos.puesto')) !!}</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="job_title" autocomplete="off" maxlength="150" info="customer-job-title"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Role --}}
                        <label for="role_id" class="col-lg-12">{!! ucfirst(trans('textos.role')) !!}</label>
                        <div class="col-lg-12">
                            @php
                            $options[] = ''; 
                            @endphp

                            @foreach($basic_roles as $role)    
                                @php
                                $options[$role->id] = $role->name;
                                @endphp 
                            @endforeach

                            <x-select name="role_id" class="select2" :options="$options" info="customer-role"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit-add-responsable" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

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