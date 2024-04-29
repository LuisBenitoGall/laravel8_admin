@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Crear nuevo grupo contable --}}
		@can('accounting-account-types.create')
			<a class="btn btn-info" href="{{ route('accounting-account-types.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.grupo_nuevo')) !!}</a>
		@endcan

		{{-- Ver tipos de cuentas contables --}}
		<a class="btn btn-info" href="{{ route('accounting-account-types.show') }}"><i class="fa fa-eye"></i> {!! ucfirst(trans('textos.cuentas_contables_tipos')) !!}</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{!! trans('textos.grupos_contables') !!} 
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccountingAccountTypes">
			<thead>
				<tr>
					<th class="wdt50">{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.nombre')) !!}</th>
					<th class="wdt50">{!! ucfirst(trans('textos.nivel')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- Edit accounting account type: --}}
@include('admin.accounting_account_types.partials.modal-edit-accounting-account-type')

@push('scripts')
<script>
$(function(){
	//Tabla Accounting Account Types:
    if($('#tblAccountingAccountTypes').length > 0){
        var tableAccountingAccounts = $('#tblAccountingAccountTypes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                type: "GET",
                url: "/api/admin/accounting-account-types",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'code', name: 'accounting_account_types.code'},
                {data: 'name', name: 'accounting_account_types.name'},
                {data: 'level', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });    
    }
	
	//Modal edición tipo cuenta:
	$(document).on('click', '.btnEditType', function(){
		var id = $(this).data('id');
		var name = $(this).data('name');
		$('#aatId').val(id);
		$('#aatName').val(name);
	});

});
</script>
@endpush

@endsection