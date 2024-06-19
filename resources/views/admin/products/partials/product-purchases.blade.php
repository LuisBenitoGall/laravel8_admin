<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductPurchases">
			<thead>
				<tr>
					<th class="text-center">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.proveedor')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.pedido')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.precio')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.descuentos')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.iva')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Product Purchases:
    if($('#tblProductPurchases').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        $('#tblProductPurchases').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-purchases/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'date', className: 'text-right'},
                {data: 'provider'},
                {data: 'order', className: 'text-right'},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'discounts', className: 'text-right'},
                {data: 'iva', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush