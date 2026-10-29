<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccountingAccountSeats">
			<thead>
				<tr>
					<th class="text-center wdt25">#</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="wdt50 text-center">{!! ucfirst(trans('textos.debe')) !!}</th>
					<th class="wdt50 text-center">{!! ucfirst(trans('textos.haber')) !!}</th>
					<th class="wdt50 text-center">{!! ucfirst(trans('textos.saldo')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Accounting Account Seats:
    if($('#tblAccountingAccountSeats').length > 0){
        var accounting_account_id = $('#myAccountingAccountId').val();

        var tableAccountingAccountSeats = $('#tblAccountingAccountSeats').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                type: "GET",
                url: "/api/admin/accounting-account-seats/"+accounting_account_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'number', className: 'text-right'},
                {data: 'date', className: 'text-right'},
                {data: 'debit', className: 'text-right'},
                {data: 'credit', className: 'text-right'},
                {data: 'balance', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                }
            ]
        });    
    }
});
</script>
@endpush