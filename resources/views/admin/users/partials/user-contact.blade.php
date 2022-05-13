<form method="post" autocomplete="off" action="{{ route('phones.store-from-user') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}
	<input type="hidden" name="user_id" value="{{ $user->id }}">

	{{-- Teléfonos --}}
	<div class="form-group row">
		<label class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.telefonos')) !!}</label>

		<div class="col-lg-1">
			<button class="btn btn-warning btn-circle btnAddPhone" rel="tooltip" title="{{ trans('textos.telefono_agregar') }}">
				<i class="fa fa-plus"></i>
			</button>
		</div>

		<div class="col-lg-9">
			<div class="phones">
				@if($phones->count() > 0)	
					@foreach($phones as $phone)
						<div class="form-group row phone">
							<label for="concept" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}</label>
							<div class="col-lg-4">
								<x-input type="text" name="concept[]" maxlength="150" value="{{ $phone->concept }}" autocomplete="off"/>
							</div>

							<label for="phone_number" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.telefono')) !!}</label>
							<div class="col-lg-2">
								<x-input type="text" name="phone_number[]" maxlength="15" value="{{ $phone->phone_number }}" autocomplete="off"/>
							</div>

							<div class="col-lg-1 pt-2">
								<button class="btn btn-danger btn-circle btn-circle-mini btnRemovePhone" rel="tooltip" title="{{ trans('textos.telefono_eliminar') }}" data-msg="{{ trans('textos.telefono_eliminar_aviso') }}">
									<i class="fa fa-times"></i>
								</button>
							</div>
						</div>
					@endforeach
				@endif
			</div>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>

{{-- HTML para clonar y añadir a previsions --}}
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

@push('scripts')
<script>
// Añadir y eliminar teléfono de usuario.
$('.btnAddPhone').on('click', function(e){
    e.preventDefault(); 
    var prev = $('.clones').html();
    $('.phones').append(prev); 
    $('.fa-spinner').remove(); 
});
$(document).on('click', '.btnRemovePhone', function(){
    $(this).closest('.phone').remove();
});
</script>
@endpush