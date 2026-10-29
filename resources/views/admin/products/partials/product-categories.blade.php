<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductCategories">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.categoria')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Product Categories:
    if($('#tblProductCategories').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        $('#tblProductCategories').DataTable({
            order: [[ 2, "desc" ]],
            serverSide: false,
            processing: false,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-categories/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'level'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush