@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="recommendationId" value="{{ $recommendation->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a recomendaciones --}}
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
				<h3 class="mb-4">
					{!! ucfirst(trans('textos.recomendacion_de')) !!} 
					<u>{{ $user->name }} {{ $user->surname }}</u>
				</h3>						

				{{-- Info --}}
				<div class="mb-3">
					{{-- Desde --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.desde')) !!}:
						<b>
							@php
							$published_at = $recommendation->published_at != ''? \Carbon\Carbon::parse($recommendation->published_at)->format($languages[session()->get('locale')][4].' H:i:s'):'';
							@endphp

							{{ $published_at }}
						</b>
					</label>

					{{-- Hasta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.hasta')) !!}:
						<b>
							@php
							$published_end = $recommendation->published_end != ''? \Carbon\Carbon::parse($recommendation->published_end)->format($languages[session()->get('locale')][4].' H:i:s'):'';
							@endphp

							{{ $published_end }}
						</b>
					</label>
				</div>

				@if($recommendation->phrase)
					<div class="form-group row">
						{{-- Cita --}}
						<label class="col-md-2 col-form-label">
							{{ trans('textos.cita') }}
							<br>
							
							@php
							$voted = !$votes || !$votes->phrase? 'btn-gray':'btn-success';
							@endphp

							<small>@if($recommendation->phrase_votes > 0) {{ $recommendation->phrase_votes }} @endif</small>
							<a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="phrase" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
								<i class="fa fa-thumbs-up"></i>
							</a>	
						</label>
						<div class="col-10">
							<p id="recom-phrase" class="bg-gray p-3">
								<i>{!! $recommendation->phrase !!}</i>
								<br>
								<b class="text-right d-block">~ {{ $recommendation->author }} ~</b>
							</p>
						</div>
		            </div>
		        @endif

	            @if($recommendation->song)
		            <div class="form-group row">
		            	{{-- Canción --}}
		            	<label class="col-md-2 col-form-label">
		            		{{ trans('textos.cancion') }}
		            		<br>

		            		@php
							$voted = !$votes || !$votes->song? 'btn-gray':'btn-success';
							@endphp

							<small>@if($recommendation->song_votes > 0) {{ $recommendation->song_votes }} @endif</small>
							<a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="song" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
								<i class="fa fa-thumbs-up"></i>
							</a>	
		            	</label>
						<div class="col-10 col-lg-6">
							<div class="video-yt">
								{!! $recommendation->song !!}
							</div>
						</div>
		            </div>
		        @endif

		        @if($recommendation->scene)
		            <div class="form-group row">
		            	{{-- Video --}}
		            	<label class="col-md-2 col-form-label">
		            		{{ trans('textos.video') }}
		            		<br>

		            		@php
							$voted = !$votes || !$votes->scene? 'btn-gray':'btn-success';
							@endphp

							<small>@if($recommendation->scene_votes > 0) {{ $recommendation->scene_votes }} @endif</small>
							<a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="scene" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
								<i class="fa fa-thumbs-up"></i>
							</a>	
		            	</label>
						<div class="col-10 col-lg-6">
							<div class="video-yt">
								{!! $recommendation->scene !!}
							</div>
						</div>
		            </div>
		        @endif

		        @if($recommendation->image)
		        	<div class="form-group row">
		            	{{-- Imagen --}}
		            	<label class="col-md-2 col-form-label">
		            		{{ ucfirst(trans('textos.imagen')) }}
		            		<br>

		            		@php
							$voted = !$votes || !$votes->image? 'btn-gray':'btn-success';
							@endphp

							<small>@if($recommendation->image_votes > 0) {{ $recommendation->image_votes }} @endif</small>
							<a href="#" class="btn btn-xs {{ $voted }} btnLike ml-1" data-item="image" rel="tooltip" title="{{ trans('textos.me_gusta') }}">
								<i class="fa fa-thumbs-up"></i>
							</a>	
		            	</label>
						<div class="col-10">
							<img src="{{ asset('/img/recommendations/'.$recommendation->image) }}" alt="">
						</div>
		            </div>
		        @endif
	        </div>
        </div>
    </div>
</div>

@endsection