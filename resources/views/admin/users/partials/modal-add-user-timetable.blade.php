<div class="modal fade modal-add-timetable" id="modal-add-timetable" tabindex="-1" role="dialog" aria-labelledby="addTimetable" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addTimetable">{!! ucfirst(trans('textos.registro_horario_add')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('timetable.add-hour') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}	
                    <input type="hidden" name="date" id="ahDate">
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

					<div class="form-group row mt-4">
						<label class="col-12">{!! ucfirst(trans('textos.conceptos_entrada')) !!} *</label>
						<div class="col-12">
							<select name="item_id_in" class="form-control select2" required>
								<option value="">{{ trans('textos.opcion_selec') }}</option>
								@foreach($obj_timetable_items as $ti)
									@if($ti->type == 1)
										<option value="{{ $ti->id }}">{{ $ti->name }}</option>
									@endif
								@endforeach	
							</select>
						</div>
					</div>

					<div class="form-group row">
                        <label class="col-12">{!! ucfirst(trans('textos.hora_entrada')) !!} *</label>
                        <div class="col-sm-2 sinpadding-right">
							<input type="text" class="form-control text-center" name="hora_in" maxlength="2" value="{{ old('hora_in') }}" required>
                        </div>
                        <div class="col-sm-1 sinpadding text-center pt-1">
                        	:
                        </div>
                        <div class="col-sm-2 sinpadding">
							<input type="text" class="form-control text-center" name="minutos_in" maxlength="2" value="{{ old('minutos_in') }}" required>
                        </div>
                        <div class="col-sm-1 sinpadding text-center pt-1">
                        	:
                        </div>
                        <div class="col-sm-2 sinpadding-left">
							<input type="text" class="form-control text-center" name="segundos_in" maxlength="2" value="{{ old('segundos_in', '00') }}" required>
                        </div>

                        @if($errors->has('hora_in') || $errors->has('minutos_in') || $errors->has('segundos_in'))
                        	<div class="col-12">
                                <span class="invalid-feedback" role="alert">
                                	<strong>{{ $errors->first('hora_in') }}{{ $errors->first('minutos_in') }}{{ $errors->first('segundos_in') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row mt-4">
						<label class="col-12">{!! ucfirst(trans('textos.conceptos_salida')) !!} *</label>
						<div class="col-12">
							<select name="item_id_out" class="form-control select2" required>
								<option value="">{{ trans('textos.opcion_selec') }}</option>
								@foreach($obj_timetable_items as $ti)
									@if($ti->type == 2)
										<option value="{{ $ti->id }}">{{ $ti->name }}</option>
									@endif
								@endforeach	
							</select>
						</div>
					</div>

					<div class="form-group row">
                        <label class="col-12">{!! ucfirst(trans('textos.hora_salida')) !!} *</label>
                        <div class="col-sm-2 sinpadding-right">
							<input type="text" class="form-control text-center" name="hora_out" maxlength="2" value="{{ old('hora_out') }}" required>
                        </div>
                        <div class="col-sm-1 sinpadding text-center pt-1">
                        	:
                        </div>
                        <div class="col-sm-2 sinpadding">
							<input type="text" class="form-control text-center" name="minutos_out" maxlength="2" value="{{ old('minutos_out') }}" required>
                        </div>
                        <div class="col-sm-1 sinpadding text-center pt-1">
                        	:
                        </div>
                        <div class="col-sm-2 sinpadding-left">
							<input type="text" class="form-control text-center" name="segundos_out" maxlength="2" value="{{ old('segundos_out', '00') }}" required>
                        </div>

                        @if($errors->has('hora_out') || $errors->has('minutos_out') || $errors->has('segundos_out'))
                        	<div class="col-12">
                                <span class="invalid-feedback" role="alert">
                                	<strong>{{ $errors->first('hora_out') }}{{ $errors->first('minutos_out') }}{{ $errors->first('segundos_out') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group row">
                        <label for="comments" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!} </label>
						<div class="col-lg-12">
							<textarea class="form-control" name="comments">{{ old('comments') }}</textarea>
						</div>
                    </div>

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>