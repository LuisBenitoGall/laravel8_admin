@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		@can('units.index')
			<a href="{{ route('user-recommendations.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_recomendaciones') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblRecommendationCompanies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.razon_social')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Recommendation Companies:
    if($('#tblRecommendationCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableRecommendationCompanies = $('#tblRecommendationCompanies').DataTable({
            order: [[ 2, "desc" ]],
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/recommendation-companies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'text-right'},
                {data: 'tradename', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection