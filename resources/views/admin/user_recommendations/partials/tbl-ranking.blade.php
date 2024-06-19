<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblRecommendations-{{ $table }}">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt200">{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th>Item</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.puntos')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Recommendations Images:
    if($('#tblRecommendations-Images').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableRecommendationsImages = $('#tblRecommendations-Images').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/recommendations-ranking/image",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-right', orderable: false, searchable: false },
                {data: 'user'},
                {data: 'item'},
                {data: 'date', className: 'text-right'},
                {data: 'points', className: 'text-right'},
            ],
            //Numerador autoincremental:
            drawCallback: function (settings) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });
    }

    //Tabla Recommendations Phrases:
    if($('#tblRecommendations-Phrases').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableRecommendationsPhrases = $('#tblRecommendations-Phrases').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/recommendations-ranking/phrase",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-right', orderable: false, searchable: false },
                {data: 'user'},
                {data: 'item'},
                {data: 'date', className: 'text-right'},
                {data: 'points', className: 'text-right'},
            ],
            //Numerador autoincremental:
            drawCallback: function (settings) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });
    }

    //Tabla Recommendations Scenes:
    if($('#tblRecommendations-Scenes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableRecommendationsScenes = $('#tblRecommendations-Scenes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/recommendations-ranking/scene",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-right', orderable: false, searchable: false },
                {data: 'user'},
                {data: 'item'},
                {data: 'date', className: 'text-right'},
                {data: 'points', className: 'text-right'},
            ],
            //Numerador autoincremental:
            drawCallback: function (settings) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });
    }

    //Tabla Recommendations Songs:
    if($('#tblRecommendations-Songs').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableRecommendationsSongs = $('#tblRecommendations-Songs').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/recommendations-ranking/song",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-right', orderable: false, searchable: false },
                {data: 'user'},
                {data: 'item'},
                {data: 'date', className: 'text-right'},
                {data: 'points', className: 'text-right'},
            ],
            //Numerador autoincremental:
            drawCallback: function (settings) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });
    }
});
</script>
@endpush