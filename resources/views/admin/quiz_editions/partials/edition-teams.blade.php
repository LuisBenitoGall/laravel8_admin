<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblEditionTeams">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.equipo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.logo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.puntos')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.tiempo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Edition Teams:
    if($('#tblEditionTeams').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        
        $('#tblEditionTeams').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-edition-teams/"+edition_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'logo', className: 'text-center'},
                {data: 'points', className: 'text-right'},
                {data: 'time', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush