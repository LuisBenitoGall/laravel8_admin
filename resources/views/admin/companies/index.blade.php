@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('companies.create')
			<a class="btn btn-info" href="{{ route('companies.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.empresa_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCompanies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.razon_social')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>NIF</th>
					<th>UTE</th>
					<th>Logo</th>
					<th>{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Transfer company --}}
@include('admin.companies.partials.modal-transfer-company')

@push('scripts')
<script>
$(function(){
	//Tabla Companies:
    if($('#tblCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        //Filters:

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblCompanies_'+window.location.pathname));

        $('#tblCompanies thead tr').clone(true).appendTo('#tblCompanies thead');
        $('#tblCompanies thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5 || i == 6){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCompanies.column(i).search() !== this.value){
                    tableCompanies
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableCompanies = $('#tblCompanies').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/companies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'alta', className: 'text-right'},
                {data: 'nif'},
                {data: 'ute', className: 'text-center'},
                {data: 'logo', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

	//Modal transferir empresa:
	$(document).on('click', '.btnTransferCompany', function(){
		var id = $(this).data('id');

		$('#companyId').val(id);
	});
});
</script>
@endpush

@endsection