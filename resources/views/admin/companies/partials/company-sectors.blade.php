<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCompanySectors">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.sector')) !!}</th>
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
	//Tabla Company Sectors:
    if($('#tblCompanySectors').length > 0){
        $('#tblCompanySectors').DataTable();
    }
});
</script>
@endpush