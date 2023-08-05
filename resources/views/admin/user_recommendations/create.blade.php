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
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('user-recommendations.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Cita --}}
						<label for="phrase" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cita')) !!}
							<br>
							<small class="text-warning">Escribe o pega el texto de la cita</small>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="phrase" class="editor"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Autor --}}
						<label for="author" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.autor_cita')) !!}
						</label>
						<div class="col-lg-10">
							<x-input type="text" name="author" autocomplete="off" maxlength="255"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Canción --}}
						<label for="song" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cancion')) !!}
							<br>
							<small class="text-warning">Inserta el código de inserción de YouTube</small>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="song"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Video --}}
						<label for="scene" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.video')) !!}
							<br>
							<small class="text-warning">Inserta el código de inserción de YouTube</small>
						</label>
						<div class="col-lg-10">
							<x-input-textarea name="scene"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Imagen --}}
						<label for="image" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.imagen')) !!}
						</label>
						<div class="col-lg-10">
							<x-input-file name="image"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection