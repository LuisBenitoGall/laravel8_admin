<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCustomerProvidersUsers">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.usuario')) }}</th>
					<th>{!! ucfirst(trans('textos.categorias')) !!}</th>
					<th>{{ ucfirst(trans('textos.email')) }}</th>
					<th>{!! ucfirst(trans('textos.telefonos')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Customer Providers Users:
    if($('#tblCustomerProvidersUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company = $('#dtCompanyId').val();

        var tableCustomerProvidersUsers = $('#tblCustomerProvidersUsers').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/customer-providers-users/"+company,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: "users.name"},
                {data: 'categories'},
                {data: 'email', name: "users.email"},
                {data: 'phones', name: "phones.phone_number"},
                {data: 'image', className: 'text-center'},
                {data: 'acciones', name: "users.id", className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush
					