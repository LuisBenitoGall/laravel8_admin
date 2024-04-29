<p class="text-warning col-12">{!! trans('textos.asigna_formulario_aviso') !!}</p>	

<div class="table-responsive">
	<table class="table table-hover dtabla" id="tblBudgetFormCustomers">
		<thead>
			<tr>
				<th>{!! ucfirst(trans('textos.cliente')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Budget Form Customers:
    if($('#tblBudgetFormCustomers').length > 0){
        var form = $('#formId').val();
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetFormCustomers thead tr').clone(true).appendTo('#tblBudgetFormCustomers thead');
        $('#tblBudgetFormCustomers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 1){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetFormCustomers.column(i).search() !== this.value){
                    tableBudgetFormCustomers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableBudgetFormCustomers = $('#tblBudgetFormCustomers').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-form-customers/"+form,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    //Formulario vinculado a cliente:
                    var vinculado = '';
                    if(row['editar']){
                        var checked = (row['budget_form_id'] == 1)? 'checked':'';
                        vinculado = '<label class="custom-chk-rdb btnSetCustomerForm" rel="tooltip" title="'+txtFormAsociarCliente+'">';
                            vinculado += '<input type="checkbox" name="budget_form_id" '+checked+'><span class="chk chk-link" data-id="'+row['id']+'" data-form="'+form+'"></span>';
                        vinculado += '</label>';
                    }

                    return vinculado;
                }
                }
            ]
        });
    }
});
</script>
@endpush