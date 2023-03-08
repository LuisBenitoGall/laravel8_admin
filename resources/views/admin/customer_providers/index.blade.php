@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
	<div class="col-12">
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

{{-- Editar configuración de proveedor: --}}
@include('admin.customer_providers.partials.modal-edit-config-provider')

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

        //Roles de la empresa:
        $.get("/admin/companies/"+id+"/roles", function(response, state){
            if(response){
                for(i=0; i<response.length; i++){
                    $("#roleId").append('<option value="'+response[i].id+'">'+response[i].name+'</option>');    
                }    
            }
        });
    });

    //Modal edición configuraciones de cliente.
    $(document).on('click', '.btnCustomerConfig', function(){
        var id = $(this).data('customer');
        var ba_id = $(this).data('baid');
        var workplace_id = $(this).data('workplaceid');
        var cc_id = $(this).data('ccid');
        var discount = $(this).data('discount');
        var budget_access = $(this).data('budgetaccess');
        var syncro = $(this).data('syncro');
        var credit = $(this).data('credit');

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

                    //Acceso a presupuestos:
                    $('#configBudgetAccess').prop('checked', budget_access);

                    //Descuento:
                    $('#configDiscount').val(discount);

                    //Riesgo crediticio:
                    $('#configCreditRisk').val(credit);

                    //Sincronización previsiones de facturación:
                    $('#configSyncroInvoice').prop('checked', syncro);
                }  
            });
        } 
    });

    //Modal de configuraciones de proveedor.
    $(document).on('click', '.btnProviderConfig', function(){
        var id = $(this).data('provider');
        var ac_id = $(this).data('acid');   //Accounting concept
        var ea_id = $(this).data('eaid');   //Cuenta de gastos
        var bank_account = $(this).data('bankaccount');

        if(id){
            $('#configProvider').val(id);

            //Conceptos contables:
            $.get("/admin/customers/"+id+"/customer-config/"+ac_id, function(response, state){
                if(response){
                    $("#configExpenseAccount option").remove().val('');
                    $('#configExpenseAccount').val('');
                    $('#configExpenseAccount').trigger('change');

                    console.log(response.concepts);
                    //Empresa:
                    $('#modal-edit-config-provider h4 b').text(response.company);

                    //Conceptos contables:
                    $('#configAccountingConcept').val(ac_id);
                    $('#configAccountingConcept').trigger('change');

                    //Cuentas de gastos:
                    if(ea_id > 0){
                        $('#configExpenseAccount').removeAttr('disabled');    
                        for (i = 0; i < response.concepts.length; i++) {
                            $("#configExpenseAccount").append('<option value="' + response.concepts[i].id + '">' + response.concepts[i].name + '</option>');
                        }
                        $('#configExpenseAccount').val(ea_id);
                        $('#configExpenseAccount').trigger('change');
                    }
                }  
            });

            //Cuentas bancarias:
            $.get("/admin/bank-accounts/"+id+"/company-accounts", function(response, state){
                if(response){
                    $("#bankAccountId option").remove();
                    //$("#bankAccountId").append('<option value=""></option>');
                    for(i=0; i<response.length; i++){
                        $("#bankAccountId").append('<option value="'+response[i].id+'">'+response[i].name+' - '+response[i].account+'</option>');    
                    }
                    $('#bankAccountId').val(bank_account);
                    $('#bankAccountId').trigger('change');
                }
            });
        }
    });

    //Cuenta de gastos por concepto contable.
    $('#configAccountingConcept').on('change', function(event){
        $("#configExpenseAccount").empty().val('');
        $('#configExpenseAccount').val('');
        $('#configExpenseAccount').trigger('change');

        $('#configAccountingConcept option:selected').each(function (){
            var id = event.target.value;   
            if (id == null || id == 0) {
                $('#configExpenseAccount').attr('disabled', 'disabled');
            } else {
                $('#configExpenseAccount').removeAttr('disabled');
                $.get("/admin/accounting-concepts/filter/" + id + "", function (response, state) {
                    $("#configExpenseAccount").append('<option value="">' + txtConceptoSelec + '</option>');
                    for (i = 0; i < response.length; i++) {
                        $("#configExpenseAccount").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                    }
                });
            }
        });
    });
});
</script>
@endpush

@endsection