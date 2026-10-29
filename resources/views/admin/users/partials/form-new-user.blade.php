{{-- Sólo SuperAdmin puede crear usuarios con permisos distintos a usuario básico --}}
@can('roles.index')
	<div class="form-group row">
		{{-- Role --}}
		<label for="roles" class="col-lg-2 col-form-label">
			{!! ucfirst(trans('textos.permisos')) !!}*
		</label>
		
		<div class="col-10">
			@foreach($roles as $rol)
				{{-- Permisos de Super Admin... --}}
				@if($rol->name == config('constants.SUPER_ADMIN_'))
					{{-- ... sólo puede ser adjudicados por otro Super Admin --}}
					@if(Auth::user()->hasRole(config('constants.SUPER_ADMIN_')))
						<label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
					        <input type="radio" name="roles" value="{{ $rol->id }}" required>
					        <span class="rdb chksmall"></span>
					        {{ $rol->name }}
					    </label>   
					@endif
				@else
					<label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
				        <input type="radio" name="roles" value="{{ $rol->id }}" required>
				        <span class="rdb chksmall"></span>

				        @php
				        $rol_ = explode('/', $rol->name);
				        @endphp
				        {!! ucfirst($rol_[1]) !!}
				    </label>   
				@endif
			@endforeach

			{!! App\Helpers\helpers::info('user-roles') !!}
		</div>	
	</div>
@else
	<input type="hidden" name="roles" value="{{ config('constants.ROLE_ID_') }}">
@endcan

<div class="form-group row">
	{{-- Nombre usuario --}}
	<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
	<div class="col-lg-4">
		<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="75" required/>
	</div>

	{{-- Apellidos --}}
	<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
	<div class="col-lg-4">
		<x-input type="text" name="surname" id="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="100" required/>
	</div>
</div>

<div class="form-group row">
	{{-- Email --}}
	<label for="email" class="col-lg-2 col-form-label">Email*</label>
	<div class="col-lg-4">
		<x-input type="email" name="email" id="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="100" required/>
	</div>
	
	{{-- Status --}}
	<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
	<div class="col-lg-1">
		<label class="custom-chk-rdb" rel="tooltip">
            <input type="checkbox" name="status" checked>
            <span class="chk" style="top: 5px;"></span>
        </label>

        <span class="relativo ml-2">
        	{!! App\Helpers\helpers::info('user-activo') !!}
        </span>
	</div>

	{{-- Envío password --}}
	<label for="send_pwd" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.usuario_envio_password')) !!}</label>
	<div class="col-lg-1">
		<label class="custom-chk-rdb" rel="tooltip">
            <input type="checkbox" name="send_pwd">
            <span class="chk" style="top: 5px;"></span>
        </label>

        <span class="relativo ml-2">
        	{!! App\Helpers\helpers::info('user-envio-password') !!}
        </span>
	</div>
</div>

<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>