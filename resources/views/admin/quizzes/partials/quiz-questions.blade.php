<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuestions">
			<thead>
				<tr>
					<th class="wdt200">{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th class="wdt300">{!! ucfirst(trans('textos.cuestion')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Questions:
    if($('#tblQuestions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var quiz_id = $('#quizId').val();
        
        var tableQuestions = $('#tblQuestions').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/questions/"+quiz_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: "questions.name"},
                {data: 'question', name: "questions.question"},
                {data: 'type'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush