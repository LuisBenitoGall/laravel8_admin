<div class="table-responsive">
	<table class="table table-hover dtabla" id="tblBudgetFormItems">
		<thead>
			<tr>
				<th>Item</th>
				<th>{!! ucfirst(trans('textos.bloque')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.descuento')) !!}</th>
				<th>{!! ucfirst(trans('textos.centro_coste')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Budget Form Items:
    if($('#tblBudgetFormItems').length > 0){
        var form = $('#formId').val();
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetFormItems thead tr').clone(true).appendTo('#tblBudgetFormItems thead');
        $('#tblBudgetFormItems thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3 || i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetFormItems.column(i).search() !== this.value){
                    tableBudgetFormItems
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableBudgetFormItems = $('#tblBudgetFormItems').DataTable({
            serverSide: true,
            processing: true, 
            //scrollX: true,
            //fixedColumns: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-form-items/"+form+"/1",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'budget_form_items.name'},
                {data: 'block', name: 'budget_form_items.name'},
                {data: 'price', className: 'text-right', name: 'products.price'},
                {data: 'discount', className: 'text-right'},
                {data: 'cost_center', name: 'cost_centers.name'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush