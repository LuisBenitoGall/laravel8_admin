<div class="modal fade modal-modify-day-type" id="modal-modify-day-type" tabindex="-1" role="dialog" aria-labelledby="modifyDayType" aria-hidden="true">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.modificar')) !!} <span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{{ trans('textos.tipo_jornada_modificar_cantidad') }}</p>

            	<form method="post" autocomplete="off" action="{{ route('day-types-users-config.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					@isset($user)
						<input type="hidden" name="user_id" value="{{ $user->id }}" id="modifiableUserId">
					@endisset
					<input type="hidden" name="year" value="{{ $year }}" id="modifiableYear">
					<input type="hidden" name="day_type_id" id="modifiableDayTypeId">

					<div class="form-group row mt-4">
						{{-- Cantidad de días --}}
						<label for="qty" class="col-lg-12">{!! ucfirst(trans('textos.cantidad')) !!}</label>	
						<div class="col-lg-12">
							<x-input type="text" name="qty" class="text-right" id="modifiableQty" autocomplete="off" maxlength="4" required/>	
						</div>
					</div>

					<div class="form-group row mb-0">
						<div class="col-12 text-right">
							@isset($user)
                            	<a href="" class="btn btn-danger btn-confirm btnDeleteModified" data-msg="{{ trans('textos.jornadas_personalizadas_confirm_eliminar') }}">
								{{ ucfirst(trans('textos.eliminar')) }}
								</a>
							@endisset

							{{-- <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" inline="true"/> --}}
							<input type="submit" name="submit_save" class="btn btn-info" value="{!! ucfirst(trans('textos.guardar')) !!}">
						</div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>