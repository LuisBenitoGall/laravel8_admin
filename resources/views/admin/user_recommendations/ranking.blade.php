@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('user-recommendations.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_recomendaciones') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-phrases" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.citas')) !!}</a>
			  		</li>

					<li class="nav-item @if($tab == 'songs') active @endif">
			    		<a class="nav-link @if($tab == 'songs') active @endif" href="#tab-songs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.canciones')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'videos') active @endif">
			    		<a class="nav-link @if($tab == 'videos') active @endif" href="#tab-videos" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.videos')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'images') active @endif">
			    		<a class="nav-link @if($tab == 'images') active @endif" href="#tab-images" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.imagenes')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-5">
					{{-- Citas --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-phrases">
						@include('admin.user_recommendations.partials.recommendation-phrases')	
					</div>

					{{-- Canciones --}}
					<div class="tab-pane fade @if($tab == 'songs') show active @endif" role="tabpanel" id="tab-songs">
						@include('admin.user_recommendations.partials.recommendation-songs')	
					</div>

					{{-- Videos --}}
					<div class="tab-pane fade @if($tab == 'videos') show active @endif" role="tabpanel" id="tab-videos">
						@include('admin.user_recommendations.partials.recommendation-videos')	
					</div>

					{{-- Imágenes --}}
					<div class="tab-pane fade @if($tab == 'images') show active @endif" role="tabpanel" id="tab-images">
						@include('admin.user_recommendations.partials.recommendation-images')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Item recommendation --}}
@include('admin.user_recommendations.partials.modal-recommendation-item')

@push('scripts')
<script>
$(function(){
	//Modal item:
	$(document).on('click', '.btnViewRecommendation', function(e){
		e.preventDefault();
		var item = $(this).data('item');
		var type = $(this).data('type');
		var html = '';

		if(type == 'image'){
			html = '<img src="/img/recommendations/'+item+'" class="img-fluid">';
		}else if(type == 'video'){
			html = item;
		}

		$('#modal-recommendation-item .modal-body').html(html);
	});
});
</script>
@endpush

@endsection