<form method="post" autocomplete="off" action="{{ route('users.update_pwd', $user->id) }}" accept-charset="utf-8" role="form">
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Password --}}
		<label for="password" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.password')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="password" name="password" id="password" maxlength="12" placeholder="Password" autocomplete="off" required/>
		</div>

		{{-- Repetir password --}}
		<label for="password_repeat" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.confirm_pwd')) !!}*</label>
		<div class="col-lg-4">
			<x-input type="password" name="password_confirmation" id="password_confirmation" maxlength="12" placeholder="{!! ucfirst(trans('textos.pwd_repite')) !!}" autocomplete="off" required/>
		</div>
	</div>
		
	<x-btn-submit name="update_pwd" label="{{ trans('textos.guardar') }}"/>		  
</form>