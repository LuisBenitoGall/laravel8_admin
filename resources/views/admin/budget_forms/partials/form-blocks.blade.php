<div class="table-responsive">
	<table class="table table-hover dtabla" id="tblBudgetFormBlocks">
		<thead>
			<tr>
				<th>{!! ucfirst(trans('textos.bloque')) !!}</th>
				<th>{!! ucfirst(trans('textos.centro_coste')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Budget Form Blocks:
    if($('#tblBudgetFormBlocks').length > 0){
        var form = $('#formId').val();
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetFormBlocks thead tr').clone(true).appendTo('#tblBudgetFormBlocks thead');
        $('#tblBudgetFormBlocks thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 2){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetFormBlocks.column(i).search() !== this.value){
                    tableBudgetFormBlocks
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableBudgetFormBlocks = $('#tblBudgetFormBlocks').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-form-items/"+form,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'cost_center', name: 'cost_centers.name'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush