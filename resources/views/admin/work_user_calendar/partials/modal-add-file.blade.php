<div class="modal fade modal-add-file" id="modal-add-file" tabindex="-1" role="dialog" aria-labelledby="modifyDayType" aria-hidden="true">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.adjuntar_documento')) !!} <span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

            	<form method="post" autocomplete="off" action="{{ route('work-user-calendar.add-doc') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="user_id" value="{{ $user->id }}">
					<input type="hidden" name="year" value="{{ $year }}">
					<input type="hidden" name="wuc_id" id="wucId">

					<div class="form-group row mt-4">
						{{-- Archivo --}}
						<label for="file" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.archivo_selec')) !!}
						</label>	
						<div class="col-lg-8">
							<x-input-file name="file"/>
						</div>

						<div class="col-12">
	                    	<small class="text-warning">
								· {{ trans('textos.formato') }}: .jpg, .png, .gif, .pdf <br>
								· {{ trans('textos.tamany_max') }}: 1000kb	<br>
							</small>

							@if($errors->has('file'))
				                <span class="invalid-feedback" role="alert">
				                  <strong>{{ $errors->first('file') }}</strong>
				                </span>
				            @endif
			            </div>
					</div>

					<x-btn-submit name="submit_add_doc" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>