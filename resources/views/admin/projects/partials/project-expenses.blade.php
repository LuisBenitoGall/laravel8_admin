<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProjectExpenses">
			<thead>
				<tr>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.concepto')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
					<th class="wdt100">{!! trans('textos.gasto_tipo') !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.documento')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Project Expenses:
    if($('#tblProjectExpenses').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var project_id = $('#myProject').val();

        $('#tblProjectExpenses').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/project-expenses/"+project_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'concept'},
                {data: 'expensed_in', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'type'},
                {data: 'provider'},
                {data: 'doc', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;

                var currency = false;
                var refund = false;
                api.rows().every(function(){
                    var data = this.data();
                    currency = '€';
                });
 
                // Remove the formatting to get integer data for summation
                var intVal = function(i){
                    //console.log(i);
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };
 
                //Total por filtro aplicado:
                total_amount = api
                .column(2, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(2).footer()).html(
                    total_amount.toFixed(2)+currency    
                );
            }
        });
    }
});
</script>
@endpush