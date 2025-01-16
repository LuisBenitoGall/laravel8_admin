<div class="table-responsive">
	<table class="table table-hover dtabla" id="tblBriefingTemplateItems">
		<thead>
			<tr>
				<th>Item</th>
				<th>{!! ucfirst(trans('textos.bloque')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!}</th>
				<th>{!! ucfirst(trans('textos.unidad')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Briefing Template Items:
    if($('#tblBriefingTemplateItems').length > 0){
        var template = {{ $template->id }};
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableBriefingTemplateItems = $('#tblBriefingTemplateItems').DataTable({
            serverSide: true,
            processing: true, 
            ajax: {
                "type": "GET",
                "url": "/api/admin/briefing-template-items/"+template,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'briefing_template_items.name'},
                {data: 'block', name: 'briefing_template_items.name'},
                {data: 'qty', className: 'text-right', name: 'briefing_template_items.expected_qty'},
                {data: 'unit', name: 'briefing_template_items.time_unit'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush