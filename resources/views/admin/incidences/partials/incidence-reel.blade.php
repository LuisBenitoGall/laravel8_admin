<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblIncidenceMessages">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th>{!! ucfirst(trans('textos.mensaje')) !!}</th>
                    <th>{!! ucfirst(trans('textos.accion_correctiva')) !!}</th>
                    <th class="text-center">{!! ucfirst(trans('textos.respuestas')) !!}</th>
                    <th>{!! ucfirst(trans('textos.autor')) !!}</th>
                    <th>{!! ucfirst(trans('textos.documentos')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Incidence Messages:
    if($('#tblIncidenceMessages').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var incidence_id = {{ $incidence->id }};
        
        $('#tblIncidenceMessages').DataTable({
            order: [[ 2, "desc" ]],
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/incidence-messages/"+incidence_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'date', className: 'text-right'},
                {data: 'message'},
                {data: 'corrective_action', className: 'text-center'},
                {data: 'responses', className: 'text-center'},
                {data: 'author'},
                {data: 'docs'},
                {data: 'acciones', className: 'text-right'}
            ],
            createdRow: function(row, data, dataIndex) {
                // Verificar si la columna corrective_action tiene la clase is-corrective
                if ($(row).find('td:eq(2) i').hasClass('is-corrective')) { // 2 es el índice de la columna corrective_action
                    $(row).css('background-color', '#d1ede7'); // Cambiar el color de fondo
                }
            }
        });
    }
});
</script>
@endpush