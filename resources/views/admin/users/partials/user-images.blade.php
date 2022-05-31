<form method="post" autocomplete="off" action="{{ route('users.store-image', $user->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}

	<div class="form-group row">
		<label for="file" class="col-lg-2 col-form-label">{{ trans('textos.usuario_foto') }}
			<br>
			<small class="text-warning">
				· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
				· {{ trans('textos.tamany_max') }}: 500kb	<br>
				· {{ trans('textos.medidas_recomendadas') }}: 200x200px							
			</small>
		</label>

		<div class="col-lg-5">
			<x-input-file name="file"/>

			@if($errors->has('file'))
          		<span class="invalid-feedback" role="alert">
            		<strong>{{ $errors->first('file') }}</strong>
          		</span>
      		@endif
		</div>	
	
		<x-btn-submit name="submit_image" label="{{ trans('textos.guardar') }}" inline="true"/>
	</div>
</form>

@if(count($images))
	<div class="row mt-5">
		@foreach($images as $img)
			<div class="col-sm-6 col-md-3 col-lg-2 foto">
				<img class="img-fluid" src="{{ asset('img/users/'.$img->image) }}">	

				<div class="controls text-right pt-2">
					{{-- Imagen destacada: --}}
					<i class="fa fa-star mr-2 btn-featured @if($img->featured == 1) featured @endif" data-img="{{ $img->image }}" data-modulo="users" rel="tooltip" title="{!! ucfirst(trans('textos.imagen_destacada')) !!}"></i>

					{{-- Imagen pública --}}
					<i class="fa fa-eye mr-2 btn-public @if($img->public == 1) public @endif" data-img="{{ $img->image }}" data-modulo="users" rel="tooltip" title="{!! ucfirst(trans('textos.imagen_publica')) !!}"></i>

					{{-- Eliminar --}}
					<a href="{{ route('users.delete-image', $img->image) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
						<i class="fa fa-trash"></i>
					</a>
				</div>
			</div>
		@endforeach
	</div>
@endif