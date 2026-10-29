@can('employees-timetable.edit')
	<script>
	var timetableItems = {!! $timetable_items !!};
	</script>

	<div class="modal fade modal-edit-timetable" id="modal-edit-timetable" tabindex="-1" role="dialog" aria-labelledby="editTimetable" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="editTimetable">{!! ucfirst(trans('textos.registro_horario_editar')) !!} <span class="userTo"></span></h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	                <form method="post" autocomplete="off" action="{{ route('timetable.update-hour') }}" accept-charset="utf-8" role="form">
	                    {{ csrf_field() }}	
	                    <input type="hidden" name="id" id="tmId">
	                    <input type="hidden" name="user_id" value="{{ $user->id }}">
	                    <input type="hidden" name="from" value="{{ $from }}">
                    	<input type="hidden" name="to" value="{{ $to }}">

						<h5 id="tmConcepto"></h5>

						<div class="form-group row mt-4">
							{{-- Conceptos horarios --}}
							<label class="col-12">{!! ucfirst(trans('textos.conceptos_horarios')) !!}</label>
							<div class="col-12">
								<x-select name="item_id" id="timetableItems" class="select2" :options="[]" required/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Hora --}}
	                        <label class="col-12">{!! ucfirst(trans('textos.hora')) !!}</label>
	                        <div class="col-sm-2 sinpadding-right">
	                        	<x-input type="text" name="hora" class="text-center" id="tmHora" autocomplete="off" maxlength="2"/>
	                        </div>
	                        <div class="col-sm-1 sinpadding text-center pt-1">
	                        	:
	                        </div>
	                        {{-- Minutos --}}
	                        <div class="col-sm-2 sinpadding">
	                        	<x-input type="text" name="minutos" class="text-center" id="tmMinutos" autocomplete="off" maxlength="2"/>
	                        </div>
	                        <div class="col-sm-1 sinpadding text-center pt-1">
	                        	:
	                        </div>
	                        {{-- Segundos --}}
	                        <div class="col-sm-2 sinpadding-left">
	                        	<x-input type="text" name="segundos" class="text-center" id="tmSegundos" autocomplete="off" maxlength="2"/>
	                        </div>

	                        @if($errors->has('hora') || $errors->has('minutos') || $errors->has('segundos'))
	                        	<div class="col-12">
	                                <span class="invalid-feedback" role="alert">
	                                	<strong>{{ $errors->first('hora') }}{{ $errors->first('minutos') }}{{ $errors->first('segundos') }}</strong>
	                                </span>
	                            </div>
                            @endif
	                    </div>

	                    <div class="form-group row">
	                    	{{-- Observaciones --}}
	                        <label for="comments" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!} </label>
							<div class="col-lg-12">
								<x-input-textarea name="comments" id="tmComments"/>
							</div>
	                    </div>

	                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
	                </form>    
	            </div>
	        </div>
	    </div>
	</div>
@endcan