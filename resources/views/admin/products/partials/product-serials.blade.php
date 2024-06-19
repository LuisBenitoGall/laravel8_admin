<div class="row">
	@if(count($stores) == 0)
		<p class="col-12 text-danger">
			{!! trans('textos.almacen_consumo_interno_inactivo_aviso') !!}
		</p>
	@endif

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductSerials">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.numero_serie')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th>{{ ucfirst(trans('textos.empleado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha_asignacion')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Product Serials:
    if($('#tblProductSerials').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        $('#tblProductSerials').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-serials/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'serial'},
                {data: 'alta', className: 'text-right'},
                {data: 'employee'},
                {data: 'assignment_date', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush