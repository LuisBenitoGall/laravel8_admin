@can('users.update')
	@php
	$update_user = '';
	@endphp
@else
	@php
	$update_user = 'disabled';
	@endphp	
@endcan

<form method="post" autocomplete="off" action="{{ route('users.update', $user->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $user->id }}">

	{{-- Sólo SuperAdmin puede crear usuarios con permisos distintos a usuario básico --}}
	@can('roles.index')
		<div class="form-group row">
			{{-- Role --}}
			<label for="roles" class="col-lg-2 col-form-label">
				{!! ucfirst(trans('textos.permisos')) !!}*
			</label>

			<div class="col-lg-10">
				@foreach($roles as $rol)
					@php
					$checked = !empty($user_roles) && $user_roles[0]->id == $rol->id? true:false;
					@endphp

					<label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
				        <input type="radio" name="roles" value="{{ $rol->id }}" @if($checked) checked @endif required>
				        <span class="rdb chksmall"></span>

				        @if($rol->name == config('constants.SUPER_ADMIN_'))
				        	{{ config('constants.SUPER_ADMIN_') }}
				        @else
					        @php
					        $rol_ = explode('/', $rol->name);
					        @endphp
				        	
				        	@if($rol_[1]) {!! ucfirst($rol_[1]) !!} @endif
				        @endif
				    </label>   
				@endforeach
			</div>
		</div>
	@else
		<input type="hidden" name="roles" value="@if(!empty($user_roles)) {{ $user_roles[0]->id }} @endif">
	@endcan

	<div class="form-group row">
		{{-- Nombre usuario --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="text" name="name" id="name" maxlength="75" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" value="{{ $user->name }}" autocomplete="off" required/>
		</div>

		{{-- Apellidos --}}
		<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="text" name="surname" id="surname" maxlength="100" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" value="{{ $user->surname }}" autocomplete="off" required/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Email --}}
		<label for="email" class="col-lg-2 col-form-label">Email*</label>
		<div class="col-lg-4">
			<x-input type="email" name="email" id="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" value="{{ $user->email }}" autocomplete="off" maxlength="100" required/>
		</div>

		{{-- Nickname --}}
		<label for="nickname" class="col-lg-2 col-form-label">Nick</label>
		<div class="col-lg-4">
			<x-input type="text" name="nickname" id="nickname" maxlength="75" placeholder="Nick" value="{{ $user->nickname }}" autocomplete="off" info="user-nick"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- NIF --}}
		<label for="nif" class="col-lg-2 col-form-label">NIF</label>
		<div class="col-lg-4">
			<x-input type="text" name="nif" id="nif" maxlength="15" placeholder="NIF" value="{{ $user->nif }}" autocomplete="off" info="user-nif"/>
		</div>

		{{-- NAF --}}
		<label for="naf" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.ss')) !!}</label>
		<div class="col-lg-4">
			<x-input type="text" name="naf" id="naf" maxlength="15" placeholder="NAF" value="{{ $user->naf }}" autocomplete="off" info="user-naf"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Fecha nacimiento --}}
		<label for="birthday" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_nacimiento')) !!}</label>
		<div class="col-lg-4">
			@php
			$birthday = $user->birthday != ''? Carbon\Carbon::parse($user->birthday)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="birthday" id="birthday" value="{{ $birthday }}" placeholder="{!! ucfirst(trans('textos.fecha_nacimiento')) !!}" autocomplete="off" :format="$languages[config('app.locale')][4]"/>
		</div>

		{{-- Categorías --}}
		@if($categories->count())
			<label for="categories" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.categorias')) !!}</label>
			<div class="col-lg-4">
				@php
				$options = [];

				$userCategories = $user_categories && $user_categories->categories? explode(',', $user_categories->categories):[];
				@endphp

				@foreach($categories as $cat)
					@php
					$options[$cat->id] = $cat->name;
					@endphp	
				@endforeach

				<x-select name="categories[]" class="select2" multiple="true" :options="$options" :values="$userCategories" info="user-categories"/>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Firma usuario --}}
		@if($user->signature)
			<label class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.firma')) !!}</label>

			<div class="col-lg-4 foto">
				<img class="img-fluid" src="{{ asset('img/signatures/'.$user->signature) }}">	

				<div class="controls text-right pt-2">
					<a href="{{ route('users.delete-signature', $user->signature) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
						<i class="fa fa-trash"></i>
					</a>
				</div>
			</div>

		@else
			@if(Auth::user()->id == $user->id)
				<label for="signature" class="col-lg-2 col-form-label">{{ trans('textos.firma') }}
					<br>
					<small class="text-warning">
						· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
						· {{ trans('textos.tamany_max') }}: 500kb <br>
						· {{ trans('textos.medidas_recomendadas') }}: 300x160px							
					</small>
				</label>

				<div class="col-lg-4">
					<x-input-file name="signature"/>

					{!! App\Helpers\helpers::info('user-signature') !!}

					@if($errors->has('signature'))
	                    <span class="invalid-feedback" role="alert">
	                      	<strong>{{ $errors->first('signature') }}</strong>
	                    </span>
	                @endif
				</div>
			@endif	
		@endif
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>