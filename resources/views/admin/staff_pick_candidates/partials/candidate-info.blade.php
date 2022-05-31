<form method="post" autocomplete="off" action="{{ route('staff-pick-candidates.update', $candidate) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $candidate->id }}">
	<input type="hidden" name="user_id" value="{{ $candidate->user_id }}">

	<div class="form-group row">
		{{-- Nombre usuario --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="75" value="{{ $user->name }}" required/>
		</div>

		{{-- Apellidos --}}
		<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="text" name="surname" id="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="100" value="{{ $user->surname }}" required/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Email --}}
		<label for="email" class="col-lg-2 col-form-label">Email*</label>
		<div class="col-lg-4">
			<x-input type="email" name="email" id="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="100" value="{{ $user->email }}" required/>
		</div>

		{{-- Valoración --}}
		<label for="assessment" class="col-lg-2 col-form-label">{{ trans('textos.valoracion') }}</label>
		<div class="col-lg-4">
			<x-rating-stars name="assessment" points="{{ $candidate->assessment }}" />
		</div>
	</div>

	<div class="form-group row">
		{{-- CV --}}
		<label for="doc" class="col-lg-2 col-form-label">CV</label>
		@if($docs->count())
			<div class="col-lg-4">
				{{-- Ver documento --}}
				<a target="_blank" class="btn btn-warning" href="{{ asset('/docs/users/'.$docs[0]->doc) }}">
					<i class="fa fa-file"></i> {{ ucfirst(trans('textos.ver')) }} CV
				</a>

				{{-- Eliminar documento --}}
				@php
				$return = 'staff-pick-candidates.edit|'.$candidate->id;
				@endphp
				<a class="btn btn-danger btn-confirm" data-msg="{{ trans('textos.documento_eliminar_confirm') }}" href="{{ route('user-docs.destroy', [$docs[0]->id, $return]) }}">
					<i class="fa fa-trash"></i> {{ ucfirst(trans('textos.eliminar')) }} CV
				</a>
			</div>
		@else
			<div class="col-lg-4">
				<x-input-file name="doc"/>

				{!! App\Helpers\helpers::info('candidate-cv') !!}

				@if($errors->has('doc'))
                    <span class="invalid-feedback" role="alert">
                      	<strong>{{ $errors->first('doc') }}</strong>
                    </span>
                @endif
			</div>
		@endif

		{{-- Imagen --}}
		<label for="image" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.imagen')) !!}</label>
		@if($images->count())
			<div class="col-lg-3">
				@foreach($images as $img)
					@if($img->featured)
						<div class="foto">
							<img class="img-fluid" src="{{ asset('img/users/'.$img->image) }}">	

							<div class="controls text-right pt-2">
								{{-- Eliminar --}}
								@php
								$return = 'staff-pick-candidates.edit|'.$candidate->id;
								@endphp

								<a href="{{ route('users.delete-image', [$img->image, $return]) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
									<i class="fa fa-trash"></i>
								</a>
							</div>
						</div>	
					@endif
				@endforeach
			</div>

		@else
			<div class="col-lg-4">
				<x-input-file name="image"/>

				{!! App\Helpers\helpers::info('candidate-image') !!}	

				@if($errors->has('image'))
                    <span class="invalid-feedback" role="alert">
                      	<strong>{{ $errors->first('image') }}</strong>
                    </span>
                @endif		
			</div>
		@endif
	</div>

	{{-- Observaciones --}}

	{{-- <div class="form-group row">
		<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea class="editor" name="observations" value="{{ $candidate->observations }}"/>
		</div>
	</div> --}}

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>