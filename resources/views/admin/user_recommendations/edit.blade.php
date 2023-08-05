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
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
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

				<form method="post" autocomplete="off" action="{{ route('user-recommendations.update', $recommendation) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Cita --}}
						<label for="phrase" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cita')) !!}
							<br>
							<small class="text-warning">Escribe o pega el texto de la cita</small>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="phrase" class="editor" value="{!! $recommendation->phrase !!}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Autor --}}
						<label for="author" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.autor_cita')) !!}
						</label>
						<div class="col-lg-10">
							<x-input type="text" name="author" autocomplete="off" maxlength="255" value="{!! $recommendation->author !!}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Canción --}}
						<label for="song" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cancion')) !!}
							<br>
							<small class="text-warning">Inserta el código de inserción de YouTube</small>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="song" value="{!! $recommendation->song !!}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Video --}}
						<label for="scene" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.video')) !!}
							<br>
							<small class="text-warning">Inserta el código de inserción de YouTube</small>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="scene" value="{!! $recommendation->scene !!}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Imagen --}}
						<label for="image" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.imagen')) !!}
						</label>
						@if($recommendation->image)
							<div class="col-lg-4 foto">			
								<img class="img-fluid" src="{{ asset('img/recommendations/'.$recommendation->image) }}" alt="">

								<div class="controls text-right pt-2">
									<a href="{{ route('user-recommendations.delete-image', $recommendation) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
										<i class="fa fa-trash"></i>
									</a>
								</div>
							</div>
						@else
							<div class="col-lg-10">
								<x-input-file name="image"/>
							</div>
						@endif
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" />				
				</form>
			</div>
		</div>
	</div>
</div>

@endsection