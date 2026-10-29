<form method="post" autocomplete="off" action="{{ route('phones.store-from-user') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}
	<input type="hidden" name="user_id" value="{{ $user->id }}">

	{{-- Teléfonos --}}
	@include('admin.phones.partials.add-phones', ['phones' => $phones])

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>

{{-- HTML para clonar y añadir a teléfonos --}}
<div class="clones" style="display: none;">
	<div class="form-group row phone">
		<label for="concept" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}</label>
		<div class="col-lg-4">
			<x-input type="text" name="concept[]" maxlength="150" autocomplete="off"/>
		</div>

		<label for="phone_number" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.telefono')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="phone_number[]" maxlength="15" autocomplete="off"/>
		</div>

		<div class="col-lg-1 pt-1">
			<button class="btn btn-danger btn-circle btn-circle-mini btnRemovePhone" rel="tooltip" title="{{ trans('textos.telefono_eliminar') }}" data-msg="{{ trans('textos.telefono_eliminar_aviso') }}">
				<i class="fa fa-times"></i>
			</button>
		</div>
	</div>
</div>
