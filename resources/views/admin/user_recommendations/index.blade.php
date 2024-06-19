@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="recommendationsEnvironment" value="{{ $environment }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Ver todas --}}
		@if($environment == 'user')
			<a href="{{ route('user-recommendations.index') }}" class="btn btn-info">
                <i class="fa fa-box-open"></i>
                {!! ucfirst(trans('textos.ver_todas')) !!}
            </a> 
        @endif 

        @if($environment == 'all')
        	{{-- Mis recomendaciones --}}
            <a href="{{ route('user-recommendations.by-user') }}" class="btn btn-info">
                <i class="fa fa-lightbulb"></i>
                {!! trans('textos.recomendaciones_mis') !!}
            </a>  
        @endif

		{{-- Nueva recomendación --}}
		<a href="{{ route('user-recommendations.create') }}" class="btn btn-info">
		    <i class="fa fa-plus"></i>
		    {!! ucfirst(trans('textos.recomendacion_nueva')) !!}
		</a>  

		{{-- Ranking --}}
        <a href="{{ route('user-recommendations.ranking') }}" class="btn btn-info">
            <i class="fa fa-trophy"></i>
            {!! ucfirst(trans('textos.ranking')) !!}
        </a>  

		{{-- Empresas --}}
		@if(Auth::user()->hasRole('Super Admin') && Auth::user()->id == 1)
			<a href="{{ route('user-recommendations.companies') }}" class="btn btn-info">
		    	<i class="fa fa-building"></i>
		    	{!! ucfirst(trans('textos.empresas_participantes')) !!}
			</a> 
		@endif
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblRecommendations">
			<thead>
				<tr>
					<th class="text-center">{{ ucfirst(trans('textos.desde')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.hasta')) }}</th>
					<th>{{ ucfirst(trans('textos.usuario')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Recommendations:
    if($('#tblRecommendations').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var environment = $('#recommendationsEnvironment').val();

        var tableRecommendations = $('#tblRecommendations').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/recommendations/"+environment,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'from', className: 'text-right'},
                {data: 'to', className: 'text-right'},
                {data: 'user'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection