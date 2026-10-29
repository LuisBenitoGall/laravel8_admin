<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductUnits">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.unidad_venta')) !!}</th>
					<th>{!! ucfirst(trans('textos.simbolo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Product Units:
    if($('#tblProductUnits').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();
        
        $('#tblProductUnits').DataTable({
            "order": [[ 2, "desc" ]],
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-units/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'unit', className: 'capitalize'},
                {data: 'symbol'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '<span style="display: none">'+row['selected']+'</span>';
                    if(row['editar']){
                        var checked = row['selected'] == 1? 'checked':'';
                        estado += '<label class="custom-chk-rdb btnSetProductUnit" rel="tooltip" title="'+txtUnidadAdoptar+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-product-unit" data-productid="'+product_id+'" data-unitid="'+row['id']+'"></span>';
                        estado += '</label>';
                    }else{
                        if(row['selected']){
                            estado = '<i class="fa fa-check text-success"></i><span style="display: none">1</span>';
                        }
                    }
                    return estado;
                }
                }
            ]
        });
    }
});
</script>
@endpush