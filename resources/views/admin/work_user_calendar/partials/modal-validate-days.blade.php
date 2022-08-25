<div class="modal fade modal-validate-days" id="modal-validate-days" tabindex="-1" role="dialog" aria-labelledby="modifyDayType" aria-hidden="true">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.jornadas_validar')) !!} <span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{{ trans('textos.jornadas_validar_texto') }}</p>

            	<form method="post" autocomplete="off" action="{{ route('work-user-calendar.validate-all') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="user_id" value="{{ $user->id }}">
					<input type="hidden" name="year" value="{{ $year }}">

					<div class="form-group row mt-4">
						{{-- Tipos de jornada --}}
						<label for="day_type" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.tipos_jornada')) !!}</label>	
						<div class="col-lg-8">
							<p style="margin-top: 6px;">
								<label class="custom-chk-rdb" rel="tooltip">
				                    <input type="checkbox" name="all" class="chkSelectAll">
				                    <span class="chk chksmall" style="top: 3px;"></span>
				                </label>
				                <b>{{ trans('textos.validar_todo') }}</b>
							</p>

							@foreach($day_types as $dt)
								<p>
									<label class="custom-chk-rdb" rel="tooltip">
					                    <input type="checkbox" name="day_type[]" value="{{ $dt->id }}" class="chkForSelecting">
					                    <span class="chk chksmall" style="top: 3px;"></span>
					                </label>
					                {{ $dt->name }}
								</p>
							@endforeach	
						</div>
					</div>

					<x-btn-submit name="submit_validate" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>