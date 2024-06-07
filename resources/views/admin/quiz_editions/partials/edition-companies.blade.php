<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblEditionCompanies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.empresa')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.participantes_num')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Edition Companies:
    if($('#tblEditionCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        
        $('#tblEditionCompanies').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-edition-companies/"+edition_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'created_at', className: 'text-right'},
                {data: 'competitors', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]  
        });
    }
});
</script>
@endpush